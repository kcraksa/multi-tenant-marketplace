<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\CartRepository;
use App\Infrastructure\Persistence\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function bootstrap(): void
    {
        //
    }
}
