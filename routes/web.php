<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Volt::route('quotes/{token}', 'quotes.show')->name('quotes.show');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Volt::route('projects', 'portal.projects.index')->name('portal.projects.index');
    Volt::route('projects/{project}', 'portal.projects.show')->name('portal.projects.show');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Volt::route('/', 'admin.dashboard')->name('admin.dashboard');
    Volt::route('leads', 'admin.leads.index')->name('admin.leads.index');
    Volt::route('projects', 'admin.projects.index')->name('admin.projects.index');
    Volt::route('projects/{project}', 'admin.projects.show')->name('admin.projects.show');
    Volt::route('team', 'admin.team.index')->name('admin.team.index');
});
