<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tuning:check-sla')]
#[Description('Flag orders past their SLA due time as breaching.')]
class CheckSlaBreaches extends Command
{
    public function handle(): int
    {
        $flagged = Order::query()
            ->where('breach', false)
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['delivered', 'refunded', 'failed'])
            ->update(['breach' => true]);

        $this->info("Flagged {$flagged} order(s) as SLA breaching.");
        return self::SUCCESS;
    }
}
