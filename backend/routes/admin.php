<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here are the admin routes for managing the YLA Umzug system.
| These routes should be protected by authentication in production.
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Settings Management
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/toggle-calculator', [SettingsController::class, 'toggleCalculator'])->name('settings.toggle-calculator');
    
    // API Routes for settings
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/settings', [SettingsController::class, 'apiIndex'])->name('settings.index');
    });
    
    // Dashboard redirect
    Route::get('/', function () {
        return redirect()->route('admin.settings.index');
    })->name('dashboard');
});