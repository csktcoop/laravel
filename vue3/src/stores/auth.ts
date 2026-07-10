import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'user'
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  // Set axios auth header
  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  const login = async (email: string, password: string) => {
    const response = await axios.post('/login', { email, password })
    const tokenValue = response.data.token as string
    token.value = tokenValue
    user.value = response.data.user
    localStorage.setItem('auth_token', tokenValue)
    axios.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
  }

  const register = async (name: string, email: string, password: string, passwordConfirmation: string) => {
    const response = await axios.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    const tokenValue = response.data.token as string
    token.value = tokenValue
    user.value = response.data.user
    localStorage.setItem('auth_token', tokenValue)
    axios.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
  }

  const logout = async () => {
    try {
      await axios.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    }
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
  }

  const fetchCurrentUser = async () => {
    try {
      const response = await axios.get('/me')
      user.value = response.data
    } catch (error) {
      console.error('Failed to fetch current user:', error)
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchCurrentUser,
  }
})
