<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-h5 text-center py-4">Login</v-card-title>
          
          <v-card-text>
            <v-form @submit.prevent="handleLogin">
              <v-text-field
                v-model="email"
                label="Email"
                type="email"
                outlined
                class="mb-4"
                required
              />
              
              <v-text-field
                v-model="password"
                label="Password"
                type="password"
                outlined
                class="mb-4"
                required
              />

              <v-alert v-if="error" type="error" class="mb-4">
                {{ error }}
              </v-alert>

              <v-btn
                type="submit"
                color="primary"
                block
                :loading="loading"
              >
                Login
              </v-btn>
            </v-form>
          </v-card-text>

          <v-card-text class="text-center">
            Don't have an account?
            <router-link to="/register">Register here</router-link>
          </v-card-text>

          <v-card-text class="text-caption mt-4">
            <div class="font-weight-bold mb-2">Test Accounts:</div>
            <div>Admin: admin@test.com / password</div>
            <div>User: user@test.com / password</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login(email.value, password.value)
    router.push('/tasks')
  } catch (err: any) {
    console.log(err);
    error.value = err.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>
