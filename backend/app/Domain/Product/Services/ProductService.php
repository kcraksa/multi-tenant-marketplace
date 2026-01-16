<?php

declare(strict_types=1);

namespace App\Domain\Product\Services;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function getPaginatedProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function getProductBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    public function createProduct(array $data): Product
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // Handle image uploads
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = $this->handleImageUploads($data['images']);
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        // Handle image uploads
        if (isset($data['images']) && is_array($data['images'])) {
            $product = $this->productRepository->find($id);
            
            // Delete old images if replacing
            if ($product && $product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $data['images'] = $this->handleImageUploads($data['images']);
        }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);
        
        if ($product && $product->images) {
            // Delete product images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        return $this->productRepository->delete($id);
    }

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActive();
    }

    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->productRepository->getFeatured($limit);
    }

    public function searchProducts(string $query): Collection
    {
        return $this->productRepository->search($query);
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        return $this->productRepository->updateStock($productId, $quantity);
    }

    public function decreaseStock(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->find($productId);
        
        if (!$product || !$product->track_inventory) {
            return true;
        }

        $newQuantity = $product->quantity - $quantity;
        
        if ($newQuantity < 0 && !$product->continue_selling) {
            return false;
        }

        return $this->productRepository->updateStock($productId, max(0, $newQuantity));
    }

    private function handleImageUploads(array $images): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $path = $image->store('products', 'public');
                $uploadedImages[] = $path;
            } elseif (is_string($image)) {
                // Image URL or path already exists
                $uploadedImages[] = $image;
            }
        }

        return $uploadedImages;
    }

    private function generateSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $count = 1;
        $originalSlug = $slug;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
