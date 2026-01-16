<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\Cart\Repositories\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(
        private Cart $cartModel,
        private CartItem $cartItemModel
    ) {}

    public function findOrCreateCart(?int $userId, ?string $sessionId): Cart
    {
        $query = $this->cartModel->where('status', 'active');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cart = $query->first();

        if (!$cart) {
            $cart = $this->cartModel->create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'status' => 'active',
            ]);
        }

        return $cart;
    }

    public function findActiveCart(?int $userId, ?string $sessionId): ?Cart
    {
        $query = $this->cartModel->where('status', 'active');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    public function addItem(Cart $cart, int $productId, int $quantity, float $price): CartItem
    {
        $existingItem = $cart->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $subtotal = $price * ($existingItem->quantity + $quantity);
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ]);
            return $existingItem->fresh();
        }

        $subtotal = $price * $quantity;
        return $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
        ]);
    }

    public function updateItem(CartItem $item, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($item);
        }

        $subtotal = $item->price * $quantity;
        return $item->update([
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ]);
    }

    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    public function clearCart(Cart $cart): bool
    {
        return $cart->items()->delete() > 0 || $cart->items()->count() === 0;
    }

    public function getCartItems(Cart $cart): Collection
    {
        return $cart->items()->with('product')->get();
    }

    public function mergeGuestCart(string $sessionId, int $userId): bool
    {
        $guestCart = $this->cartModel
            ->where('session_id', $sessionId)
            ->where('status', 'active')
            ->first();

        if (!$guestCart) {
            return false;
        }

        $userCart = $this->findOrCreateCart($userId, null);

        foreach ($guestCart->items as $item) {
            $this->addItem($userCart, $item->product_id, $item->quantity, $item->price);
        }

        $guestCart->update(['status' => 'completed']);

        return true;
    }
}
