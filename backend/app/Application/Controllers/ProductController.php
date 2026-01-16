<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Requests\ProductRequest;
use App\Domain\Product\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        
        if ($request->has('search')) {
            $products = $this->productService->searchProducts($request->get('search'));
            return $this->successResponse($products);
        }

        $products = $this->productService->getPaginatedProducts((int) $perPage);

        return $this->successResponse($products);
    }

    /**
     * Get active products for customers.
     */
    public function active()
    {
        $products = $this->productService->getActiveProducts();
        return $this->successResponse($products);
    }

    /**
     * Get featured products.
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 10);
        $products = $this->productService->getFeaturedProducts((int) $limit);
        return $this->successResponse($products);
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        if (is_numeric($id)) {
            $product = $this->productService->getProductById((int) $id);
        } else {
            $product = $this->productService->getProductBySlug($id);
        }

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product);
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, int $id)
    {
        try {
            $updated = $this->productService->updateProduct($id, $request->validated());

            if (!$updated) {
                return $this->errorResponse('Product not found', 404);
            }

            $product = $this->productService->getProductById($id);
            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return $this->errorResponse('Product not found', 404);
            }

            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }
}
