<template>
  <el-container class="app-shell">
    <el-header class="app-header">
      <div class="brand">Team Task Manager</div>
      <div class="header-actions">
        <el-dropdown
          v-if="authStore.isAuthenticated"
          trigger="click"
          @command="handleMenuCommand"
        >
          <span class="user-menu-trigger">
            <el-icon><User /></el-icon>
            <span>{{ authStore.user?.name }}</span>
          </span>
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item disabled
                >{{ authStore.user?.name }}
                ({{ authStore.user?.role }})</el-dropdown-item
              >
              <el-dropdown-item command="logout">Logout</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
      </div>
    </el-header>
    <el-main>
      <router-view />
    </el-main>
  </el-container>
</template>

<script setup lang="ts">
import { useUserStore } from "@domains/user";
import { User } from "@element-plus/icons-vue";
import { onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const authStore = useUserStore();

onMounted(async () => {
  if (authStore.token && !authStore.user) {
    await authStore.fetchCurrentUser();
  }
});

const handleMenuCommand = async (command: string) => {
  if (command === "logout") {
    await authStore.logout();
    router.push("/login");
  }
};
</script>

<style scoped>
.app-shell {
  min-height: 100vh;
}

.app-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #409eff;
  color: white;
  padding: 0 24px;
}

.brand {
  font-size: 18px;
  font-weight: 600;
}

.user-menu-trigger {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  color: white;
}
</style>
