<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Ecu;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderFile;
use App\Models\SiteSetting;
use App\Models\Tune;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Notifications\OrderQueued;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class NewOrderWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public ?int $makeId    = null;
    public ?int $modelId   = null;
    public ?int $vehicleId = null;
    public ?int $ecuId     = null;
    /** @var array<string> */
    public array  $tuneSlugs = [];
    public string $note      = '';
    public string $paymentMethod = 'credits';

    #[Validate('required|file|max:10240')] // 10MB max
    public $upload = null;

    public function next(): void
    {
        if ($this->step === 1 && ! $this->vehicleId) {
            $this->addError('vehicleId', 'Pick a vehicle.');
            return;
        }
        if ($this->step === 2 && ! $this->ecuId) {
            $this->addError('ecuId', 'Pick an ECU.');
            return;
        }
        if ($this->step === 3 && empty($this->tuneSlugs)) {
            $this->addError('tuneSlugs', 'Pick at least one tune.');
            return;
        }
        $this->step++;
    }

    public function back(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function updatedMakeId(): void
    {
        $this->modelId = null;
        $this->vehicleId = null;
        $this->ecuId = null;
    }

    public function updatedModelId(): void
    {
        $this->vehicleId = null;
        $this->ecuId = null;
    }

    public function updatedVehicleId(): void
    {
        $this->ecuId = null;
    }

    public function toggleTune(string $slug): void
    {
        $i = array_search($slug, $this->tuneSlugs, true);
        if ($i === false) $this->tuneSlugs[] = $slug;
        else array_splice($this->tuneSlugs, $i, 1);
    }

    public function submit(): void
    {
        $this->validate(['upload' => 'required|file|max:10240']);

        $user = auth()->user();
        $vehicle = Vehicle::find($this->vehicleId);
        $ecu     = Ecu::find($this->ecuId);
        $tunes   = Tune::whereIn('slug', $this->tuneSlugs)->get();
        $cost    = (int) $tunes->sum('credit_cost');

        $balance = (int) ($user->customerProfile?->credit_balance ?? 0);
        $payPerFileEnabled = SiteSetting::get('pay_per_file_enabled', 'true') === 'true';

        if ($this->paymentMethod === 'credits' && $balance < $cost) {
            $this->addError('upload', "Not enough credits (need {$cost}, you have {$balance}). Buy more credits first or select 'Pay now'.");
            return;
        }

        $next = (int) (Order::max('reference') ?? 4000) + 1;

        $path = $this->upload->store('ecu-files/'.now()->format('Y/m'), 'local');
        $md5  = md5_file($this->upload->getRealPath());

        $order = Order::create([
            'reference'      => (string) $next,
            'customer_id'    => $user->id,
            'reseller_id'    => $user->reseller_id,
            'vehicle_id'     => $vehicle?->id,
            'ecu_id'         => $ecu?->id,
            'status'         => 'queued',
            'origin'         => 'customer upload',
            'vehicle_label'  => $vehicle?->displayName(),
            'vehicle_year'   => $vehicle?->year_start,
            'ecu_label'      => $ecu?->identifier,
            'options_label'  => $tunes->pluck('label')->implode(' + '),
            'options'        => $tunes->pluck('slug')->all(),
            'credits_cost'   => $cost,
            'file_size'      => number_format($this->upload->getSize() / 1_048_576, 2).' MB',
            'sla'            => '30m',
            'progress'       => 0,
            'queued_at'      => now(),
            'sla_due_at'     => now()->addMinutes(30),
            'customer_note'  => $this->note ?: null,
        ]);

        // Auto-assign: if customer belongs to a tenant, assign the tenant user as tuner
        // Otherwise, find the first active tuner
        if ($user->reseller_id) {
            $order->update(['assigned_tuner_id' => $user->reseller_id, 'assigned_at' => now()]);
        } else {
            $firstTuner = \App\Models\TunerProfile::where('status', '!=', 'off')->first();
            if ($firstTuner) {
                $order->update(['assigned_tuner_id' => $firstTuner->user_id, 'assigned_at' => now()]);
            }
        }

        OrderFile::create([
            'order_id'       => $order->id,
            'uploaded_by_id' => $user->id,
            'kind'           => 'original',
            'disk'           => 'local',
            'path'           => $path,
            'original_name'  => $this->upload->getClientOriginalName(),
            'size'           => $this->upload->getSize(),
            'md5'            => $md5,
            'mime'           => $this->upload->getMimeType(),
        ]);

        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => $user->id,
            'stage'       => 'file received',
            'state'       => 'done',
            'note'        => 'customer upload · '.$order->file_size.' · md5 ok',
            'happened_at' => now(),
        ]);
        OrderEvent::create([
            'order_id'    => $order->id,
            'stage'       => 'validated',
            'state'       => 'done',
            'note'        => 'ECU id matches '.$ecu?->identifier,
            'happened_at' => now(),
        ]);

        if ($this->paymentMethod === 'credits') {
            // Deduct credits (existing flow)
            if ($user->customerProfile) {
                $user->customerProfile->decrement('credit_balance', $cost);

                CreditTransaction::create([
                    'user_id'        => $user->id,
                    'order_id'       => $order->id,
                    'type'           => 'spend',
                    'credits'        => -$cost,
                    'balance_after'  => $user->customerProfile->credit_balance,
                    'amount_pennies' => $cost * (int) SiteSetting::get('credit_rate_pennies', '100'),
                    'note'           => "Order #{$order->reference}",
                ]);
            }

            $user->notify(new OrderQueued($order));

            // Notify tenant if order is from a sub-customer
            if ($user->reseller_id) {
                $reseller = User::find($user->reseller_id);
                if ($reseller) {
                    $reseller->notify(new OrderQueued($order));
                }
            }

            $this->redirect(route('app.orders.show', $order), navigate: true);
        } else {
            // Pay-per-file: calculate GBP cost in pennies
            $creditRatePennies = (int) SiteSetting::get('credit_rate_pennies', '100');
            $amountPennies = $cost * $creditRatePennies;

            CreditTransaction::create([
                'user_id'        => $user->id,
                'order_id'       => $order->id,
                'type'           => 'spend',
                'credits'        => -$cost,
                'balance_after'  => $balance,
                'amount_pennies' => $amountPennies,
                'payment_method' => 'stripe',
                'payment_status' => 'pending',
                'note'           => "Pay-per-file for order #{$order->reference} — awaiting payment",
            ]);

            $user->notify(new OrderQueued($order));

            if ($user->reseller_id) {
                $reseller = User::find($user->reseller_id);
                if ($reseller) {
                    $reseller->notify(new OrderQueued($order));
                }
            }

            // Dev mode: no Stripe — order created but payment pending (needs admin/reseller approval)
            if (! config('cashier.secret')) {
                $this->redirect(route('app.orders.show', $order), navigate: true);
                return;
            }

            // Production: redirect to Stripe checkout
            $checkout = $user->checkoutCharge(
                $amountPennies,
                "Tune order #{$order->reference}",
                1,
                [
                    'success_url' => route('app.orders.show', $order),
                    'cancel_url'  => route('app.orders.show', $order),
                    'metadata'    => ['order_id' => $order->id, 'user_id' => $user->id],
                ]
            );

            $this->redirect($checkout->url, navigate: false);
        }
    }

    public function render()
    {
        $makes = VehicleMake::where('is_active', true)
            ->whereHas('models', fn ($q) => $q->where('is_active', true)->whereHas('variants', fn ($qq) => $qq->where('is_active', true)))
            ->orderBy('name')->get();

        $models = $this->makeId
            ? VehicleModel::where('make_id', $this->makeId)
                ->where('is_active', true)
                ->whereHas('variants', fn ($q) => $q->where('is_active', true))
                ->orderBy('name')->get()
            : collect();

        $variants = $this->modelId
            ? Vehicle::where('model_id', $this->modelId)
                ->where('is_active', true)
                ->orderBy('year_start', 'desc')->get()
            : collect();

        $totalCost = (int) Tune::whereIn('slug', $this->tuneSlugs)->sum('credit_cost');
        $balance   = (int) (auth()->user()->customerProfile?->credit_balance ?? 0);
        $creditRatePennies = (int) SiteSetting::get('credit_rate_pennies', '100');
        $payPerFileEnabled = SiteSetting::get('pay_per_file_enabled', 'true') === 'true';

        return view('livewire.new-order-wizard', [
            'makes'    => $makes,
            'models'   => $models,
            'variants' => $variants,
            'ecus'     => $this->vehicleId
                ? Vehicle::find($this->vehicleId)?->ecus()->get() ?? collect()
                : collect(),
            'tunes'         => Tune::where('is_active', true)->get(),
            'totalCost'     => $totalCost,
            'balance'       => $balance,
            'payPerFileEnabled' => $payPerFileEnabled,
            'pricePounds'   => number_format(($totalCost * $creditRatePennies) / 100, 2),
        ]);
    }
}
