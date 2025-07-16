<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Calculator API Routes
Route::prefix('calculator')->group(function () {
    Route::post('/calculate', [CalculatorController::class, 'calculate']);
    Route::get('/services', [CalculatorController::class, 'getServices']);
    Route::get('/enabled', [SettingsController::class, 'isCalculatorEnabled']);
});

// Quote Management Routes
Route::prefix('quotes')->group(function () {
    Route::post('/submit', [QuoteController::class, 'submit']);
    Route::get('/', [QuoteController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/{quote}', [QuoteController::class, 'show'])->middleware('auth:sanctum');
    Route::patch('/{quote}/status', [QuoteController::class, 'updateStatus'])->middleware('auth:sanctum');
});

// Settings API Routes
Route::prefix('settings')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SettingsController::class, 'index']);
    Route::post('/', [SettingsController::class, 'update']);
});

// Admin API Routes
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/email/status', [AdminController::class, 'getEmailStatus']);
    Route::post('/email/test', [AdminController::class, 'sendTestEmail']);
});