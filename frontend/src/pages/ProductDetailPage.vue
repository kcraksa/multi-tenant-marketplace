<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading product details...</p>
    </div>

    <div v-else-if="product" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Product Image -->
      <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
        <img
          v-if="product.image"
          :src="product.image"
          :alt="product.name"
          class="w-full h-full object-cover"
        />
        <div v-else class="flex items-center justify-center h-full">
          <span class="text-gray-400 text-lg">No image available</span>
        </div>
      </div>

      <!-- Product Info -->
      <div class="flex flex-col">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>
        
        <div class="mb-6">
          <span class="text-3xl font-bold text-indigo-600">${{ product.price }}</span>
          <span v-if="product.stock > 0" class="ml-4 text-green-600">
            In Stock ({{ product.stock }} available)
          </span>
          <span v-else class="ml-4 text-red-600">Out of Stock</span>
        </div>

        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
          <p class="text-gray-700 whitespace-pre-wrap">{{ product.description }}</p>
        </div>

        <div v-if="product.sku" class="mb-6">
          <p class="text-sm text-gray-600">SKU: {{ product.sku }}</p>
        </div>

        <!-- Add to Cart -->
        <div class="flex items-center gap-4 mt-auto">
          <div class="flex items-center border border-gray-300 rounded-lg">
            <button
              @click="decreaseQuantity"
              :disabled="quantity <= 1"
              class="px-4 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              -
            </button>
            <input
              v-model.number="quantity"
              type="number"
              min="1"
              :max="product.stock"
              class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none"
            />
            <button
              @click="increaseQuantity"
              :disabled="quantity >= product.stock"
              class="px-4 py-2 text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              +
            </button>
          </div>

          <button
            @click="addToCart"
            :disabled="product.stock <= 0 || adding"
            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
          >
            {{ adding ? 'Adding...' : 'Add to Cart' }}
          </button>
        </div>

        <button
          @click="$router.push('/products')"
          class="mt-4 text-indigo-600 hover:text-indigo-700 font-medium"
        >
          ← Back to Products
        </button>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-600">Product not found</p>
      <button
        @click="$router.push('/products')"
        class="mt-4 text-indigo-600 hover:text-indigo-700 font-medium"
      >
        Back to Products
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productAPI, cartAPI } from '../api'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const product = ref(null)
const loading = ref(true)
const quantity = ref(1)
const adding = ref(false)

const increaseQuantity = () => {
  if (quantity.value < product.value.stock) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const addToCart = async () => {
  try {
    adding.value = true
    await cartAPI.addItem({
      product_id: product.value.id,
      quantity: quantity.value,
    })
    await cartStore.fetchCart()
    alert('Product added to cart!')
    quantity.value = 1
  } catch (error) {
    console.error('Error adding to cart:', error)
    alert('Failed to add product to cart')
  } finally {
    adding.value = false
  }
}

onMounted(async () => {
  try {
    const response = await productAPI.getById(route.params.id)
    product.value = response.data.data
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
})
</script>
