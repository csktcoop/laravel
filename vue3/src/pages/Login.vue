<template>
  <div class="auth-page">
    <el-card class="auth-card">
      <template #header>
        <div class="auth-header">Login</div>
      </template>

      <el-form :model="formLogin" label-width="0" @submit.prevent="handleLogin">
        <el-form-item>
          <el-input v-model="formLogin.email" placeholder="Email" type="email" />
        </el-form-item>
        <el-form-item>
          <el-input
            v-model="formLogin.password"
            placeholder="Password"
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
          Login
        </el-button>
      </el-form>

      <div class="auth-links">
        <router-link to="/register">Create an account</router-link>
      </div>

      <div class="test-accounts">
        <div>Test Accounts:</div>
        <div>Admin: admin@test.com / password</div>
        <div>User: user@test.com / password</div>
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

const formLogin = reactive({ email: "", password: "" });
const loading = ref(false);
const error = ref("");

const handleLogin = async () => {
  loading.value = true;
  error.value = "";

  try {
    await authStore.login(formLogin.email, formLogin.password);
    router.push("/tasks");
  } catch (err: unknown) {
    error.value = getApiErrorMessage(err, "Login failed");
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

.test-accounts {
  margin-top: 16px;
  color: #606266;
  font-size: 13px;
  line-height: 1.6;
}

.w-full {
  width: 100%;
}

.mb-4 {
  margin-bottom: 16px;
}
</style>
