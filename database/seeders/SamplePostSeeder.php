<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamplePostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::role('admin')->first();

        Post::firstOrCreate(
            ['slug' => 'what-stage-1-actually-means'],
            [
                'title'           => 'What "stage 1" actually means (and what it doesn\'t)',
                'excerpt'         => 'Stage 1 is the most-sold tune in the industry — and the most misunderstood. Here\'s what it actually changes inside your ECU and what hardware it assumes you have.',
                'seo_description' => 'Stage 1 ECU remap explained: what the tuner changes, what hardware it assumes, and the typical gains across petrol and diesel platforms.',
                'cover_image'     => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=1200&q=80',
                'author_id'       => $author?->id,
                'is_published'    => true,
                'published_at'    => now()->subDays(3),
                'body' => <<<'MD'
A "stage 1" map is a software-only ECU remap that assumes the car is otherwise stock — same intake, same exhaust, same intercooler, same fuel quality you already buy. It's not a hardware upgrade. It's a recalibration of the parameters the OEM already shipped, dialled to where they should have been if marketing didn't care about insurance bracket fuel-economy tests.

## What actually changes

A good stage 1 file moves four things:

- **Boost target** — the requested manifold pressure is raised, usually 0.2–0.4 bar above stock peak for petrol, less for diesel.
- **Ignition timing** — a few extra degrees of advance up to the knock threshold. Pulled back automatically by the ECU's knock sensor logic if you run rubbish fuel.
- **Fuel maps** — enriched slightly to match the new air mass, keeping lambda in the safe window under load.
- **Torque limiters** — the OEM's deliberate ceilings (gearbox protection, emissions cycle pass) are raised within hardware tolerance.

## What it isn't

It's not a power-on-demand button. It's a different shape of curve — usually flatter and broader. A stock 2.0 TSI making 220 hp peak might make 270 hp peak after stage 1, but the bigger story is the 80 Nm of extra mid-range torque that arrives 700 rpm earlier.

It also isn't a free lunch. Higher cylinder pressure stresses bearings, head gaskets, and turbos. On a well-maintained car this is fine. On a 130k-mile car that's never had a timing chain service, it's not.

## What's actually safe to ask for

Anything that respects the **stock hardware envelope**:

- Boost gains up to about +25 % on petrol, +15 % on diesel
- Removing the 250 km/h electronic limiter (if legal where you drive)
- Pops & bangs on overrun (cosmetic — does shorten cat life)

What's not stage 1:

- DPF / EGR / AdBlue delete — these are emissions equipment changes, illegal on the road in the UK/EU
- Raising the rev limit — needs hardware on most engines
- "Stage 1+" with intake & downpipe — that's stage 2 territory

If your tuner is offering you 150 hp on top of stock with a stage 1 file, they're either lying about hardware requirements or doing something unsafe.
MD,
            ],
        );
    }
}
