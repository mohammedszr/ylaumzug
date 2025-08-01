<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'YLA Umzug Backend API',
        'version' => '1.0.0',
        'status' => 'active'
    ]);
});

// Include admin routes
require __DIR__.'/admin.php';

// Authentication Routes would go here if needed
// For now, using API-based authentication with Sanctum