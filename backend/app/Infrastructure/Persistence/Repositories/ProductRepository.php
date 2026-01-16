<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Product $model
    ) {}

    public function all(): Collection
    {
        return $this->model->latest()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Product
    {
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $product = $this->find($id);
        
        if (!$product) {
            return false;
        }

        // Update slug if name changed
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $product->update($data);
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->latest()->get();
    }

    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->active()->featured()->limit($limit)->get();
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->latest()
            ->get();
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }

        return $product->update(['quantity' => $quantity]);
    }
}
