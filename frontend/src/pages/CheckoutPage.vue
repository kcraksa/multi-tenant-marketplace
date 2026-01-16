<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div v-if="cartStore.items.length === 0" class="text-center py-12">
      <p class="text-lg text-gray-600 mb-6">Your cart is empty</p>
      <router-link
        to="/products"
        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
      >
        Continue Shopping
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Checkout Form -->
      <div class="lg:col-span-2">
        <!-- Shipping Information -->
        <div class="card mb-6">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Information</h2>
          <form @submit.prevent="submitOrder" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Full Name
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="John Doe"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Email
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="john@example.com"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number
              </label>
              <input
                v-model="form.phone"
                type="tel"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="+1 (555) 123-4567"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Address
              </label>
              <input
                v-model="form.address"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="123 Main Street"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  City
                </label>
                <input
                  v-model="form.city"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="New York"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  State/Province
                </label>
                <input
                  v-model="form.state"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="NY"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Postal Code
                </label>
                <input
                  v-model="form.postal_code"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="10001"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Country
              </label>
              <input
                v-model="form.country"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="United States"
              />
            </div>

            <!-- Payment Method -->
            <div class="pt-6 border-t">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.payment_method"
                    value="credit_card"
                    class="h-4 w-4 text-blue-600"
                  />
                  <span class="ml-3 text-gray-700">Credit/Debit Card</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.payment_method"
                    value="bank_transfer"
                    class="h-4 w-4 text-blue-600"
                  />
                  <span class="ml-3 text-gray-700">Bank Transfer</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="form.payment_method"
                    value="cash"
                    class="h-4 w-4 text-blue-600"
                  />
                  <span class="ml-3 text-gray-700">Cash on Delivery</span>
                </label>
              </div>
            </div>

            <div class="pt-6">
              <button
                type="submit"
                :disabled="loading"
                class="w-full btn-primary disabled:opacity-50"
              >
                {{ loading ? 'Processing...' : 'Place Order' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="card sticky top-4">
          <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
          <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
            <div
              v-for="item in cartStore.items"
              :key="item.id"
              class="flex justify-between text-sm"
            >
              <div>
                <p class="font-medium text-gray-900">{{ item.product?.name }}</p>
                <p class="text-gray-600">Qty: {{ item.quantity }}</p>
              </div>
              <p class="font-semibold">${{ (item.price * item.quantity).toFixed(2) }}</p>
            </div>
          </div>

          <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-semibold">${{ cartStore.cartTotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Shipping</span>
              <span class="font-semibold">$0.00</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Tax</span>
              <span class="font-semibold">$0.00</span>
            </div>
            <div class="border-t pt-2 flex justify-between text-lg font-bold">
              <span>Total</span>
              <span>${{ cartStore.cartTotal.toFixed(2) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const loading = ref(false)
const form = reactive({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: authStore.user?.phone || '',
  address: authStore.user?.address || '',
  city: authStore.user?.city || '',
  state: authStore.user?.state || '',
  postal_code: authStore.user?.postal_code || '',
  country: authStore.user?.country || '',
  payment_method: 'credit_card',
})

const submitOrder = async () => {
  loading.value = true
  try {
    // For now, just clear the cart and show success message
    // In a real app, this would create an order in the backend
    await cartStore.clearCart()
    alert('Order placed successfully! Thank you for your purchase.')
    router.push('/')
  } catch (error) {
    alert('Error placing order: ' + error.message)
  } finally {
    loading.value = false
  }
}
</script>
