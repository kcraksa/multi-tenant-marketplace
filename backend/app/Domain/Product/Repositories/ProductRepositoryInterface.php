<?php

declare(strict_types=1);

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function find(int $id): ?Product;
    
    public function findBySlug(string $slug): ?Product;
    
    public function create(array $data): Product;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getActive(): Collection;
    
    public function getFeatured(int $limit = 10): Collection;
    
    public function search(string $query): Collection;
    
    public function updateStock(int $productId, int $quantity): bool;
}
