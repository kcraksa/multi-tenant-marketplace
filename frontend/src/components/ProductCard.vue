<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer" @click="goToProduct">
    <div class="aspect-w-1 aspect-h-1 bg-gray-200">
      <img
        v-if="product.primary_image"
        :src="getImageUrl(product.primary_image)"
        :alt="product.name"
        class="w-full h-48 object-cover"
      />
      <div v-else class="w-full h-48 flex items-center justify-center text-gray-400">
        No Image
      </div>
    </div>
    
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">
        {{ product.name }}
      </h3>
      
      <p v-if="product.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ product.description }}
      </p>
      
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xl font-bold text-gray-900">
            ${{ product.price }}
          </p>
          <p v-if="product.compare_at_price" class="text-sm text-gray-500 line-through">
            ${{ product.compare_at_price }}
          </p>
        </div>
        
        <button
          @click.stop="addToCart"
          :disabled="!canAddToCart"
          class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Add to Cart
        </button>
      </div>
      
      <div v-if="product.discount_percentage" class="mt-2">
        <span class="inline-block px-2 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded">
          Save {{ product.discount_percentage }}%
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
})

const router = useRouter()
const cartStore = useCartStore()

const canAddToCart = computed(() => {
  if (!props.product.track_inventory) return true
  if (props.product.continue_selling) return true
  return props.product.quantity > 0
})

const goToProduct = () => {
  router.push(`/products/${props.product.id}`)
}

const addToCart = async () => {
  const result = await cartStore.addToCart(props.product.id, 1)
  if (result.success) {
    alert('Product added to cart!')
  } else {
    alert(result.message)
  }
}

const getImageUrl = (path) => buildStorageUrl(path)
</script>
