<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <div v-if="cartStore.items.length === 0" class="text-center py-12">
      <svg
        class="mx-auto h-12 w-12 text-gray-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
        />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No items in cart</h3>
      <p class="mt-1 text-sm text-gray-500">Start shopping to add items to your cart.</p>
      <div class="mt-6">
        <router-link
          to="/products"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-4">
        <div
          v-for="item in cartStore.items"
          :key="item.id"
          class="card flex items-center space-x-4"
        >
          <img
            v-if="item.product?.primary_image"
            :src="getImageUrl(item.product.primary_image)"
            :alt="item.product.name"
            class="w-24 h-24 object-cover rounded"
          />
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ item.product?.name }}
            </h3>
            <p class="text-sm text-gray-600">${{ item.price }}</p>
          </div>
          <div class="flex items-center space-x-2">
            <button
              @click="updateQuantity(item, item.quantity - 1)"
              class="px-2 py-1 border rounded hover:bg-gray-100"
            >
              -
            </button>
            <span class="px-4">{{ item.quantity }}</span>
            <button
              @click="updateQuantity(item, item.quantity + 1)"
              class="px-2 py-1 border rounded hover:bg-gray-100"
            >
              +
            </button>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-gray-900">
              ${{ (item.price * item.quantity).toFixed(2) }}
            </p>
            <button
              @click="removeItem(item.id)"
              class="text-sm text-red-600 hover:underline"
            >
              Remove
            </button>
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="card sticky top-4">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
          <div class="space-y-2 mb-4">
            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-semibold">${{ cartStore.cartTotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Shipping</span>
              <span class="font-semibold">$0.00</span>
            </div>
            <div class="border-t pt-2 flex justify-between text-lg font-bold">
              <span>Total</span>
              <span>${{ cartStore.cartTotal.toFixed(2) }}</span>
            </div>
          </div>
          <router-link
            to="/checkout"
            class="block w-full text-center btn-primary mb-2"
          >
            Proceed to Checkout
          </router-link>
          <button
            @click="clearCart"
            class="w-full btn-secondary"
          >
            Clear Cart
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../stores/cart'
import { buildStorageUrl } from '@/utils/apiBase'

const cartStore = useCartStore()

const updateQuantity = async (item, newQuantity) => {
  if (newQuantity < 1) return
  await cartStore.updateQuantity(item.id, newQuantity)
}

const removeItem = async (itemId) => {
  if (confirm('Remove this item from cart?')) {
    await cartStore.removeItem(itemId)
  }
}

const clearCart = async () => {
  if (confirm('Clear all items from cart?')) {
    await cartStore.clearCart()
  }
}

const getImageUrl = (path) => buildStorageUrl(path)
</script>
