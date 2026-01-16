<?php

declare(strict_types=1);

namespace App\Domain\Cart\Repositories;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;

interface CartRepositoryInterface
{
    public function findOrCreateCart(?int $userId, ?string $sessionId): Cart;
    
    public function findActiveCart(?int $userId, ?string $sessionId): ?Cart;
    
    public function addItem(Cart $cart, int $productId, int $quantity, float $price): CartItem;
    
    public function updateItem(CartItem $item, int $quantity): bool;
    
    public function removeItem(CartItem $item): bool;
    
    public function clearCart(Cart $cart): bool;
    
    public function getCartItems(Cart $cart);
    
    public function mergeGuestCart(string $sessionId, int $userId): bool;
}
