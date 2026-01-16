import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { cartAPI } from '../api'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const total = ref(0)
  const totalItems = ref(0)

  const cartCount = computed(() => totalItems.value)
  const cartTotal = computed(() => total.value)

  const fetchCart = async () => {
    try {
      const response = await cartAPI.getCart()
      const cart = response.data.data
      
      items.value = cart.items || []
      total.value = cart.total || 0
      totalItems.value = cart.total_items || 0
    } catch (error) {
      console.error('Fetch cart error:', error)
    }
  }

  const addToCart = async (productId, quantity = 1) => {
    try {
      const response = await cartAPI.addItem({ product_id: productId, quantity })
      await fetchCart()
      return { success: true, message: 'Item added to cart' }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to add item to cart'
      }
    }
  }

  const updateQuantity = async (itemId, quantity) => {
    try {
      await cartAPI.updateItem(itemId, quantity)
      await fetchCart()
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update item'
      }
    }
  }

  const removeItem = async (itemId) => {
    try {
      await cartAPI.removeItem(itemId)
      await fetchCart()
      return { success: true, message: 'Item removed from cart' }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to remove item'
      }
    }
  }

  const clearCart = async () => {
    try {
      await cartAPI.clearCart()
      items.value = []
      total.value = 0
      totalItems.value = 0
      return { success: true }
    } catch (error) {
      return { success: false }
    }
  }

  const mergeCart = async () => {
    try {
      await cartAPI.mergeCart()
      await fetchCart()
    } catch (error) {
      console.error('Merge cart error:', error)
    }
  }

  return {
    items,
    total,
    totalItems,
    cartCount,
    cartTotal,
    fetchCart,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart,
    mergeCart,
  }
})
