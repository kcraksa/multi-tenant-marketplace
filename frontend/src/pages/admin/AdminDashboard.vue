<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
          <nav class="flex space-x-4">
            <router-link
              to="/admin/products"
              class="px-3 py-2 rounded-md text-sm font-medium"
              :class="$route.path.includes('/admin/products') 
                ? 'bg-indigo-100 text-indigo-700' 
                : 'text-gray-700 hover:bg-gray-100'"
            >
              Products
            </router-link>
            <router-link
              to="/admin/tenants"
              class="px-3 py-2 rounded-md text-sm font-medium"
              :class="$route.path.includes('/admin/tenants') 
                ? 'bg-indigo-100 text-indigo-700' 
                : 'text-gray-700 hover:bg-gray-100'"
            >
              Tenants
            </router-link>
            <router-link
              to="/"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100"
            >
              Back to Store
            </router-link>
          </nav>
        </div>
      </div>
    </header>

    <!-- Admin Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

onMounted(() => {
  // Redirect to products by default if on admin root
  if (router.currentRoute.value.path === '/admin') {
    router.push('/admin/products')
  }
})
</script>
