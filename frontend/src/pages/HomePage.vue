<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">
        Welcome to Multi-Tenant eCommerce
      </h1>
      <p class="text-xl text-gray-600">
        Discover amazing products at great prices
      </p>
    </div>

    <div class="mb-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Products</h2>
      <div v-if="loading" class="text-center py-8">
        <p class="text-gray-600">Loading products...</p>
      </div>
      <div v-else-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
        />
      </div>
      <div v-else class="text-center py-8">
        <p class="text-gray-600">No featured products available</p>
      </div>
    </div>

    <div class="text-center">
      <router-link
        to="/products"
        class="inline-block px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700"
      >
        View All Products
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { productAPI } from '../api'
import ProductCard from '../components/ProductCard.vue'

const products = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await productAPI.getFeatured(8)
    products.value = response.data.data
  } catch (error) {
    console.error('Error fetching featured products:', error)
  } finally {
    loading.value = false
  }
})
</script>
