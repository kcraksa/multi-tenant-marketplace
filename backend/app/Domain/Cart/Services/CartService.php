<?php

declare(strict_types=1);

namespace App\Domain\Cart\Services;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    public function getOrCreateCart(?int $userId, ?string $sessionId): Cart
    {
        return $this->cartRepository->findOrCreateCart($userId, $sessionId);
    }

    public function getActiveCart(?int $userId, ?string $sessionId): ?Cart
    {
        return $this->cartRepository->findActiveCart($userId, $sessionId);
    }

    public function addToCart(int $productId, int $quantity, ?int $userId, ?string $sessionId): array
    {
        try {
            DB::beginTransaction();

            $product = $this->productRepository->find($productId);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            if (!$product->isActive()) {
                throw new \Exception('Product is not available');
            }

            if ($product->track_inventory && $product->quantity < $quantity && !$product->continue_selling) {
                throw new \Exception('Insufficient stock');
            }

            $cart = $this->getOrCreateCart($userId, $sessionId);
            $cartItem = $this->cartRepository->addItem($cart, $productId, $quantity, (float) $product->price, $product->toArray());

            DB::commit();

            return [
                'success' => true,
                'cart' => $cart->fresh(['items.product']),
                'item' => $cartItem,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function updateCartItem(int $itemId, int $quantity): array
    {
        try {
            $item = CartItem::find($itemId);

            if (!$item) {
                throw new \Exception('Cart item not found');
            }

            $product = $item->product;

            if ($product->track_inventory && $product->quantity < $quantity && !$product->continue_selling) {
                throw new \Exception('Insufficient stock');
            }

            $this->cartRepository->updateItem($item, $quantity);

            return [
                'success' => true,
                'cart' => $item->cart->fresh(['items.product']),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function removeFromCart(int $itemId): array
    {
        try {
            $item = CartItem::find($itemId);

            if (!$item) {
                throw new \Exception('Cart item not found');
            }

            $cart = $item->cart;
            $this->cartRepository->removeItem($item);

            return [
                'success' => true,
                'cart' => $cart->fresh(['items.product']),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function clearCart(?int $userId, ?string $sessionId): bool
    {
        $cart = $this->getActiveCart($userId, $sessionId);

        if (!$cart) {
            return false;
        }

        return $this->cartRepository->clearCart($cart);
    }

    public function getCartWithItems(?int $userId, ?string $sessionId): ?Cart
    {
        $cart = $this->getActiveCart($userId, $sessionId);

        if ($cart) {
            $cart->load('items.product');
        }

        return $cart;
    }

    public function mergeGuestCart(string $sessionId, int $userId): bool
    {
        return $this->cartRepository->mergeGuestCart($sessionId, $userId);
    }

    public function getCartTotal(?int $userId, ?string $sessionId): float
    {
        $cart = $this->getActiveCart($userId, $sessionId);

        return $cart ? $cart->total : 0.0;
    }

    public function getCartItemsCount(?int $userId, ?string $sessionId): int
    {
        $cart = $this->getActiveCart($userId, $sessionId);

        return $cart ? $cart->total_items : 0;
    }
}
