<template>
  <div class="min-h-screen flex flex-col">
    <header class="bg-white shadow-sm">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-2xl font-bold text-blue-600">
              eCommerce
            </router-link>
            <div class="ml-10 flex space-x-4">
              <router-link
                to="/"
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-blue-600"
              >
                Home
              </router-link>
              <router-link
                to="/products"
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-blue-600"
              >
                Products
              </router-link>
              <router-link
                v-if="authStore.isAdmin"
                to="/admin"
                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-blue-600"
              >
                Admin
              </router-link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              to="/cart"
              class="relative p-2 text-gray-700 hover:text-blue-600"
            >
              <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
              <span
                v-if="cartStore.cartCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
              >
                {{ cartStore.cartCount }}
              </span>
            </router-link>
            <div v-if="authStore.isAuthenticated" class="flex items-center space-x-2">
              <span class="text-sm text-gray-700">{{ authStore.user?.name }}</span>
              <button
                @click="handleLogout"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
              >
                Logout
              </button>
            </div>
            <router-link
              v-else
              to="/login"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
            >
              Login
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <main class="flex-1">
      <router-view />
    </main>

    <footer class="bg-gray-800 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center">&copy; 2026 Multi-Tenant eCommerce. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

onMounted(() => {
  cartStore.fetchCart()
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
