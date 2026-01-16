<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="card">
      <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
      
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">
          {{ error }}
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
            v-model="form.email"
            type="email"
            required
            class="input-field"
            placeholder="your@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
            v-model="form.password"
            type="password"
            required
            class="input-field"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full btn-primary disabled:opacity-50"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-gray-600">
        Don't have an account?
        <router-link to="/register" class="text-blue-600 hover:underline">
          Register
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const form = ref({
  email: '',
  password: '',
})

const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  const result = await authStore.login(form.value)

  if (result.success) {
    await cartStore.mergeCart()
    await cartStore.fetchCart()
    router.push('/')
  } else {
    error.value = result.message
  }

  loading.value = false
}
</script>
