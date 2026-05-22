<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* Marketing */
Route::view('/', 'marketing.welcome')->name('home');

/* Post-login router (Breeze redirects here as 'dashboard') */
Route::get('/dashboard', function () {
    $u = Auth::user();
    if (! $u) return redirect()->route('login');
    return $u->isAdmin() || $u->isTuner()
        ? redirect()->route('admin.live')
        : redirect()->route('app.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

/* Customer area */
Route::middleware(['auth', 'verified', 'role:customer|admin'])
    ->prefix('app')->name('app.')->group(function () {
        Route::view('/', 'app.dashboard')->name('dashboard');
        Route::view('/orders', 'app.orders.index')->name('orders.index');
        Route::view('/orders/new', 'app.orders.new')->name('orders.new');
        Route::view('/credits', 'app.credits')->name('credits');
    });

/* Admin / Operations / Tuner area */
Route::middleware(['auth', 'verified', 'role:admin|operations|tuner'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::view('/overview',  'admin.overview')->name('overview');
        Route::view('/live',      'admin.live')->name('live');
        Route::view('/queue',     'admin.queue')->name('queue');
        Route::view('/customers', 'admin.customers')->name('customers');
        Route::view('/files',     'admin.placeholder')->name('files');
        Route::view('/tuners',    'admin.placeholder')->name('tuners');
        Route::view('/vehicles',  'admin.placeholder')->name('vehicles');
        Route::view('/disputes',  'admin.placeholder')->name('disputes');
        Route::view('/tickets',   'admin.placeholder')->name('tickets');
        Route::view('/revenue',   'admin.placeholder')->name('revenue');
        Route::view('/credits',   'admin.placeholder')->name('credits');
        Route::view('/reports',   'admin.placeholder')->name('reports');
    });

require __DIR__.'/auth.php';
