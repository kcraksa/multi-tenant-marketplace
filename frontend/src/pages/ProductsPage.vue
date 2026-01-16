<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">All Products</h1>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading products...</p>
    </div>

    <div v-else-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-600">No products available</p>
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
    const response = await productAPI.getActive()
    products.value = response.data.data
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
})
</script>
