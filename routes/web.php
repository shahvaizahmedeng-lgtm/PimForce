<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('landing');
})->name('home');


// Dashboard route (starter template)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Integrations: list, create, show, and delete
    Route::get('integrations', [\App\Http\Controllers\IntegrationController::class, 'index'])->name('integrations.index');
    Volt::route('integrations/create', 'integration-stepper')->name('integrations.create');
    Route::get('integrations/{integration}', [\App\Http\Controllers\IntegrationController::class, 'show'])->name('integrations.show');
    Route::delete('integrations/{integration}', [\App\Http\Controllers\IntegrationController::class, 'destroy'])->name('integrations.destroy');
});

require __DIR__.'/auth.php';
