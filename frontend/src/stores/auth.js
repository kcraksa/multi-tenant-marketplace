import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authAPI } from '../api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => {
    if (!user.value) return false
    // Check for roles array (from API with eager loading)
    if (user.value.roles && Array.isArray(user.value.roles)) {
      return user.value.roles.some(role => role.name === 'admin')
    }
    // Fallback: check for admin role directly
    return user.value.roles?.includes('admin') || false
  })

  const initializeAuth = () => {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')
    
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  const login = async (credentials) => {
    try {
      const response = await authAPI.login(credentials)
      const { user: userData, token: userToken } = response.data.data
      
      user.value = userData
      token.value = userToken
      
      localStorage.setItem('token', userToken)
      localStorage.setItem('user', JSON.stringify(userData))
      
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed'
      }
    }
  }

  const register = async (data) => {
    try {
      const response = await authAPI.register(data)
      const { user: userData, token: userToken } = response.data.data
      
      user.value = userData
      token.value = userToken
      
      localStorage.setItem('token', userToken)
      localStorage.setItem('user', JSON.stringify(userData))
      
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Registration failed',
        errors: error.response?.data?.errors
      }
    }
  }

  const logout = async () => {
    try {
      await authAPI.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  const fetchUser = async () => {
    try {
      const response = await authAPI.me()
      user.value = response.data.data
      localStorage.setItem('user', JSON.stringify(user.value))
    } catch (error) {
      console.error('Fetch user error:', error)
      logout()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    initializeAuth,
    login,
    register,
    logout,
    fetchUser,
  }
})
