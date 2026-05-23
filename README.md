# Tuning Files

ECU tuning files platform built in 14 phases: marketing site + customer area + admin/ops/tuner dashboard with kanban, queue, drawer, customers 3-pane, and Stripe-backed credit packs.

## Stack
- Laravel 13, Breeze (Livewire 3), Alpine.js
- Spatie Permission (admin / operations / tuner / customer)
- Laravel Cashier (Stripe) for credit-pack purchases
- Design CSS from the bundled hi-fi prototype (Inter + JetBrains Mono, OKLCH palette, light + dark)
- SQLite for dev (swap to MySQL via `.env`), local-disk file storage under `storage/app/private/ecu-files`

## Run it
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed     # creates demo users, vehicles, ECUs, orders
npm run build                        # or npm run dev for HMR
php artisan serve
```

## Demo accounts (password: `password`)
| Role | Email |
| --- | --- |
| Admin / Operations | `admin@tuningfiles.test` |
| Tuner | `aleks@tuningfiles.test` |
| Customer | `jamie@example.com` |

## Routes
- `/` — marketing landing (hero with queue preview, pricing tiers, vendor grid, CTA)
- `/login`, `/register` — split-layout auth, 2-step register
- `/app` — customer dashboard
  - `/app/orders/new` — 4-step upload wizard
  - `/app/orders/{order}` — timeline + download
  - `/app/credits` — buy credit packs
- `/admin` — operations area
  - `/admin/live` — kanban + tuner workload
  - `/admin/queue` — filterable table with KPI strip
  - `/admin/overview` — KPIs + bar/donut + top customers + activity
  - `/admin/customers` — 3-pane (list / detail / order preview)

## Stripe
Set `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` and `stripe_price_id` on each `credit_packs` row to enable real Checkout. Without those, purchases run in dev mode (atomic credit grant + ledger entry, no Stripe call).

## SLA breach checker
```bash
php artisan tuning:check-sla         # one-off
# or rely on the scheduled job (every 5 minutes — see routes/console.php)
```

## Theme
Dark mode toggles per user via `localStorage` (no flash on load — pre-paint script in `partials/head.blade.php`).
