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

// Include admin routes FIRST - before fallback routes
require __DIR__.'/admin.php';

// Serve the React frontend for the root path
Route::get('/', function () {
    return file_exists(public_path('index.html')) 
        ? response()->file(public_path('index.html'))
        : response()->json([
            'message' => 'YLA Umzug Backend API',
            'version' => '1.0.0',
            'status' => 'active'
        ]);
});

// Serve static assets (CSS, JS, images)
Route::get('/assets/{path}', function ($path) {
    $filePath = public_path('assets/' . $path);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    abort(404);
})->where('path', '.*');

// Serve other static files (robots.txt, sitemap.xml, etc.)
Route::get('/{file}', function ($file) {
    $allowedFiles = ['robots.txt', 'sitemap.xml', 'llms.txt'];
    if (in_array($file, $allowedFiles) && file_exists(public_path($file))) {
        return response()->file(public_path($file));
    }
    abort(404);
});

// Fallback route for React Router - serve index.html for all other routes
// This should be LAST so it doesn't interfere with admin routes
// Exclude admin routes and API routes from the fallback
Route::fallback(function () {
    $request = request();
    
    // Don't serve frontend for admin routes or API routes
    if (str_starts_with($request->path(), 'admin/') || str_starts_with($request->path(), 'api/')) {
        abort(404);
    }
    
    if (file_exists(public_path('index.html'))) {
        return response()->file(public_path('index.html'));
    }
    return response()->json([
        'message' => 'Page not found',
        'status' => 'error'
    ], 404);
});

// Authentication Routes would go here if needed
// For now, using API-based authentication with Sanctum