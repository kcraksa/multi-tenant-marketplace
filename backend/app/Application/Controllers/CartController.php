<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Requests\AddToCartRequest;
use App\Application\Requests\UpdateCartItemRequest;
use App\Domain\Cart\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Get the current cart.
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $cart = $this->cartService->getCartWithItems($userId, $sessionId);

        if (!$cart) {
            return $this->successResponse([
                'items' => [],
                'total' => 0,
                'total_items' => 0,
            ]);
        }

        return $this->successResponse([
            'id' => $cart->id,
            'items' => $cart->items,
            'total' => $cart->total,
            'total_items' => $cart->total_items,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function addItem(AddToCartRequest $request)
    {
        $userId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $result = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity,
            $userId,
            $sessionId
        );

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        return $this->successResponse([
            'cart' => $result['cart'],
            'item' => $result['item'],
        ], 'Item added to cart successfully');
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(UpdateCartItemRequest $request, int $itemId)
    {
        $result = $this->cartService->updateCartItem($itemId, $request->quantity);

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        return $this->successResponse($result['cart'], 'Cart item updated successfully');
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $itemId)
    {
        $result = $this->cartService->removeFromCart($itemId);

        if (!$result['success']) {
            return $this->errorResponse($result['message'], 400);
        }

        return $this->successResponse($result['cart'], 'Item removed from cart successfully');
    }

    /**
     * Clear the cart.
     */
    public function clear(Request $request)
    {
        $userId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        $this->cartService->clearCart($userId, $sessionId);

        return $this->successResponse(null, 'Cart cleared successfully');
    }

    /**
     * Merge guest cart with user cart after login.
     */
    public function merge(Request $request)
    {
        if (!$request->user()) {
            return $this->errorResponse('User not authenticated', 401);
        }

        $sessionId = $request->session()->getId();
        $merged = $this->cartService->mergeGuestCart($sessionId, $request->user()->id);

        if ($merged) {
            return $this->successResponse(null, 'Cart merged successfully');
        }

        return $this->successResponse(null, 'No guest cart to merge');
    }
}
