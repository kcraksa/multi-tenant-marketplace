<?php

use App\Application\Controllers\AuthController;
use App\Application\Controllers\CartController;
use App\Application\Controllers\ProductController;
use App\Application\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

// API routes for development (direct on localhost)
Route::prefix('api')->group(function () {
    // Authentication routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public product routes
    Route::get('/products', [ProductController::class, 'active']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Cart routes (both guest and authenticated)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{id}', [CartController::class, 'updateItem']);
        Route::delete('/items/{id}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });

    // Protected routes requiring authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/cart/merge', [CartController::class, 'merge']);
    });

    // Admin-only routes for product management (exclude GET for public, POST/PUT/DELETE for admin)
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::apiResource('tenants', TenantController::class);
    });
});

// Welcome page
Route::get('/', function () {
    return view('welcome');
});
