<?php

use App\Application\Controllers\AuthController;
use App\Application\Controllers\CartController;
use App\Application\Controllers\ProductController;
use App\Application\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

// Tenant-specific routes
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
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

            // Protected routes
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('/auth/logout', [AuthController::class, 'logout']);
                Route::get('/auth/me', [AuthController::class, 'me']);
                Route::post('/cart/merge', [CartController::class, 'merge']);

                // Admin routes
                Route::middleware('role:admin')->group(function () {
                    Route::apiResource('products', ProductController::class)->except(['show']);
                    Route::apiResource('tenants', TenantController::class);
                });
            });
        });
    });
}

