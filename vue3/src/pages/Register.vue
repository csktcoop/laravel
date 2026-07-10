<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card>
          <v-card-title class="text-h5 text-center py-4">Register</v-card-title>
          
          <v-card-text>
            <v-form @submit.prevent="handleRegister">
              <v-text-field
                v-model="name"
                label="Name"
                outlined
                class="mb-4"
                required
              />
              
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

              <v-text-field
                v-model="passwordConfirmation"
                label="Confirm Password"
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
                Register
              </v-btn>
            </v-form>
          </v-card-text>

          <v-card-text class="text-center">
            Already have an account?
            <router-link to="/login">Login here</router-link>
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

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match'
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    await authStore.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/tasks')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>
