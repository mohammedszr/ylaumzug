<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SettingsController;
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
    
    // PDF Generation Routes (Admin only)
    Route::post('/{quote}/generate-pdf', [QuoteController::class, 'generatePdf'])->middleware('auth:sanctum');
    Route::get('/{quote}/download-pdf', [QuoteController::class, 'downloadPdf'])->middleware('auth:sanctum')->name('quotes.download-pdf');
    Route::get('/{quote}/preview-pdf', [QuoteController::class, 'previewPdf'])->middleware('auth:sanctum');
    Route::post('/{quote}/send-pdf', [QuoteController::class, 'sendPdfQuote'])->middleware('auth:sanctum');
});

// Public Settings API Routes
Route::prefix('settings')->group(function () {
    Route::get('/public', [SettingsController::class, 'getPublicSettings']);
});