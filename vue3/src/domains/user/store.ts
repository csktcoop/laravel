import api, {
  initApiClient, buildRequestConfig, setAuthToken } from "@/shared/api";
import { defineStore } from "pinia";
import { computed, ref } from "vue";
import type { User } from "@/domains/user/model";
import { authStorage } from "@/shared/util/auth-storage";

export const useUserStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(authStorage.get());

  const isAuthenticated = computed(() => !!token.value);

  if (token.value) {
    setAuthToken(token.value);
  }

  const login = async (email: string, password: string) => {
    const response = await api.post(
      "/login",
      { email, password },
      buildRequestConfig(),
    );
    const tokenValue = response.data.token as string;
    token.value = tokenValue;
    user.value = response.data.user;
    authStorage.set(tokenValue);
    setAuthToken(tokenValue);
  };

  const register = async (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string,
  ) => {
    const response = await api.post("/register", {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    });
    const tokenValue = response.data.token as string;
    token.value = tokenValue;
    user.value = response.data.user;
    authStorage.set(tokenValue);
    setAuthToken(tokenValue);
  };

  const logout = async () => {
    try {
      await api.post("/logout", undefined, buildRequestConfig());
    } catch (error) {
      console.error("Logout error:", error);
    }
    token.value = null;
    user.value = null;
    authStorage.remove();
    setAuthToken(null);
  };

  initApiClient()
  const fetchCurrentUser = async () => {
    try {
      const response = await api.get("/me", buildRequestConfig());
      user.value = response.data;
    } catch (error) {
      console.error("Failed to fetch current user:", error);
      token.value = null;
      user.value = null;
      authStorage.remove();
      setAuthToken(null);
    }
  };

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchCurrentUser,
  };
});
