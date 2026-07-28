<template>
  <div class="auth-page">
    <el-card class="auth-card">
      <template #header>
        <div class="auth-header">Register</div>
      </template>

      <el-form :model="form" label-width="0" @submit.prevent="handleRegister">
        <el-form-item>
          <el-input v-model="form.name" placeholder="Name" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="form.email" placeholder="Email" type="email" />
        </el-form-item>
        <el-form-item>
          <el-input
            v-model="form.password"
            placeholder="Password"
            type="password"
          />
        </el-form-item>
        <el-form-item>
          <el-input
            v-model="form.passwordConfirmation"
            placeholder="Confirm password"
            type="password"
          />
        </el-form-item>

        <el-alert
          v-if="error"
          :title="error"
          type="error"
          show-icon
          class="mb-4"
        />

        <el-button
          type="primary"
          native-type="submit"
          :loading="loading"
          class="w-full"
        >
          Register
        </el-button>
      </el-form>

      <div class="auth-links">
        <router-link to="/login">Already have an account?</router-link>
      </div>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@/domains";
import { getApiErrorMessage } from "@/shared/api";

const router = useRouter();
const authStore = useUserStore();

const form = reactive({
  name: "",
  email: "",
  password: "",
  passwordConfirmation: "",
});
const loading = ref(false);
const error = ref("");

const handleRegister = async () => {
  if (form.password !== form.passwordConfirmation) {
    error.value = "Passwords do not match";
    return;
  }

  loading.value = true;
  error.value = "";

  try {
    await authStore.register(
      form.name,
      form.email,
      form.password,
      form.passwordConfirmation,
    );
    router.push("/tasks");
  } catch (err: unknown) {
    error.value = getApiErrorMessage(err, "Registration failed");
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.auth-page {
  min-height: calc(100vh - 60px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-card {
  width: min(100%, 460px);
}

.auth-header {
  font-size: 20px;
  font-weight: 600;
  text-align: center;
}

.auth-links {
  margin-top: 12px;
  text-align: center;
}

.w-full {
  width: 100%;
}

.mb-4 {
  margin-bottom: 16px;
}
</style>
