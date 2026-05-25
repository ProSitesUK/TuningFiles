<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* Marketing */
Route::view('/', 'marketing.welcome')->name('home');
Route::get('/vehicles', function () {
    $makes = \App\Models\VehicleMake::where('is_active', true)
        ->whereHas('models', fn ($q) => $q->where('is_active', true)->whereHas('variants', fn ($qq) => $qq->where('is_active', true)))
        ->withCount(['models' => fn ($q) => $q->where('is_active', true)])
        ->orderBy('name')
        ->get();
    return view('marketing.vehicles', ['makes' => $makes]);
})->name('vehicles');
Route::get('/vehicles/{make:slug}', [\App\Http\Controllers\VehiclePagesController::class, 'showMake'])->name('vehicles.make');
Route::get('/vehicles/{make:slug}/{model:slug}', [\App\Http\Controllers\VehiclePagesController::class, 'showModel'])
    ->scopeBindings()
    ->name('vehicles.model');

Route::get('/blog',          [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::get('/results', [\App\Http\Controllers\DynoResultsController::class, 'index'])->name('results');

Route::view('/about', 'marketing.about')->name('about');
Route::view('/contact', 'marketing.contact')->name('contact');
Route::view('/terms', 'marketing.terms')->name('terms');
Route::view('/privacy', 'marketing.privacy')->name('privacy');
Route::view('/refunds', 'marketing.refunds')->name('refunds');

Route::get('/become-a-tuner', [\App\Http\Controllers\TunerSignupController::class, 'show'])->name('tuner.signup');
Route::post('/become-a-tuner', [\App\Http\Controllers\TunerSignupController::class, 'store']);

/* SEO */
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt',  [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

/* Post-login router (Breeze redirects here as 'dashboard') */
Route::get('/dashboard', function () {
    $u = Auth::user();
    if (! $u) return redirect()->route('login');
    if ($u->isAdmin() || $u->isTuner()) return redirect()->route('admin.live');
    if ($u->isReseller()) return redirect()->route('reseller.dashboard');
    return redirect()->route('app.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::post('/logout', function (\App\Livewire\Actions\Logout $logout) {
    $logout();
    return redirect('/');
})->middleware('auth')->name('logout');

/* Customer area */
Route::middleware(['auth', 'verified', 'role:customer|admin'])
    ->prefix('app')->name('app.')->group(function () {
        Route::view('/', 'app.dashboard')->name('dashboard');
        Route::view('/orders', 'app.orders.index')->name('orders.index');
        Route::view('/orders/new', 'app.orders.new')->name('orders.new');
        Route::get('/orders/{order}', function (\App\Models\Order $order) {
            abort_unless($order->customer_id === auth()->id() || auth()->user()->isAdmin(), 403);
            return view('app.orders.show', ['order' => $order]);
        })->name('orders.show');
        Route::view('/credits', 'app.credits')->name('credits');
        Route::view('/referrals', 'app.referrals')->name('referrals');
        Route::view('/tickets', 'app.tickets.index')->name('tickets.index');
        Route::view('/tickets/new', 'app.tickets.new')->name('tickets.new');
        Route::get('/tickets/{ticket}', function (\App\Models\Ticket $ticket) {
            abort_unless($ticket->customer_id === auth()->id() || auth()->user()->isAdmin(), 403);
            return view('app.tickets.show', ['ticket' => $ticket]);
        })->name('tickets.show');
        Route::post('/checkout/{pack}', [\App\Http\Controllers\CheckoutController::class, 'start'])->name('checkout.start');
        Route::get('/checkout/success', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
    });

/* Admin / Operations / Tuner area */
Route::middleware(['auth', 'verified', 'role:admin|operations|tuner'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::view('/overview',  'admin.overview')->name('overview');
        Route::view('/live',      'admin.live')->name('live');
        Route::view('/queue',     'admin.queue')->name('queue');
        Route::view('/customers', 'admin.customers')->name('customers');
        Route::view('/files',     'admin.files')->name('files');
        Route::view('/tuners',    'admin.tuners')->name('tuners');
        Route::view('/vehicles',  'admin.vehicles')->name('vehicles');
        Route::view('/disputes',  'admin.disputes')->name('disputes');
        Route::view('/tickets',   'admin.tickets')->name('tickets');
        Route::view('/revenue',   'admin.revenue')->name('revenue');
        Route::view('/credits',   'admin.credits')->name('credits');
        Route::view('/reports',   'admin.reports')->name('reports');
        Route::view('/blog',      'admin.blog')->name('blog');
        Route::view('/dyno-results', 'admin.dyno-results')->name('dyno-results');
        Route::view('/seo',       'admin.seo')->name('seo');
        Route::view('/resellers', 'admin.resellers')->name('resellers');
        Route::view('/settings',  'admin.settings')->name('settings');
    });

/* Reseller area */
// To require subscription: add \App\Http\Middleware\TenantSubscribed::class to the middleware array below
Route::middleware(['auth', 'verified', 'role:reseller'])
    ->prefix('reseller')->name('reseller.')->group(function () {
        Route::view('/',          'reseller.dashboard')->name('dashboard');
        Route::view('/customers', 'reseller.customers')->name('customers');
        Route::view('/customers/invite', 'reseller.invite')->name('invite');
        Route::view('/orders',    'reseller.orders')->name('orders');
        Route::get('/orders/{order}', function (\App\Models\Order $order) {
            abort_unless($order->reseller_id === auth()->id(), 403);
            return view('reseller.order', ['order' => $order]);
        })->name('orders.show');
        Route::view('/pricing',   'reseller.pricing')->name('pricing');
        Route::view('/settings',  'reseller.settings')->name('settings');

        // Subscription billing
        Route::get('/plans', [\App\Http\Controllers\TenantSubscriptionController::class, 'plans'])->name('plans');
        Route::post('/subscribe/{plan:slug}', [\App\Http\Controllers\TenantSubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::get('/billing', [\App\Http\Controllers\TenantSubscriptionController::class, 'billing'])->name('billing');
        Route::post('/cancel', [\App\Http\Controllers\TenantSubscriptionController::class, 'cancel'])->name('cancel');
    });

/* Tenant customer portal (white-label) */
Route::prefix('t/{tenant:slug}')->name('tenant.')->group(function () {
    Route::get('/login',  [\App\Http\Controllers\TenantAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\TenantAuthController::class, 'login']);
    Route::get('/register',  [\App\Http\Controllers\TenantAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\TenantAuthController::class, 'register']);

    Route::middleware(['auth'])->group(function () {
        Route::get('/',           [\App\Http\Controllers\TenantCustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',     [\App\Http\Controllers\TenantCustomerController::class, 'orders'])->name('orders');
        Route::get('/orders/new', [\App\Http\Controllers\TenantCustomerController::class, 'newOrder'])->name('orders.new');
        Route::get('/orders/{order}', [\App\Http\Controllers\TenantCustomerController::class, 'showOrder'])->name('orders.show');
        Route::get('/credits',    [\App\Http\Controllers\TenantCustomerController::class, 'credits'])->name('credits');
        Route::get('/tickets',    [\App\Http\Controllers\TenantCustomerController::class, 'tickets'])->name('tickets');
    });
});

require __DIR__.'/auth.php';
