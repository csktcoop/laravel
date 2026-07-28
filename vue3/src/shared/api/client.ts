import axios, { type AxiosError, type AxiosRequestConfig, isAxiosError } from "axios";
import { authStorage } from "@/shared/util/auth-storage";

const api = axios.create({
  baseURL: "/api",
  headers: {
    Accept: "application/json",
  },
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error?.response?.status === 401 || error?.response?.status === 403) {
      setAuthToken(null);
      authStorage.remove();

      if (typeof window !== "undefined" && window.location.pathname !== "/login") {
        window.location.assign("/login");
      }
    }

    return Promise.reject(error);
  },
);

export const createAbortController = (): AbortController => new AbortController();

export const buildRequestConfig = (controller?: AbortController): AxiosRequestConfig => {
  if (!controller) {
    return {};
  }

  return {
    signal: controller.signal,
  };
};

export const isCancellationError = (error: unknown): boolean => {
  return axios.isCancel(error) || (error as AxiosError)?.code === "ERR_CANCELED";
};

export const getApiErrorMessage = (error: unknown, fallback: string): string => {
  if (isAxiosError(error)) {
    return error.response?.data?.message ?? fallback;
  }

  return fallback;
};

export const setAuthToken = (token: string | null): void => {
  if (token) {
    api.defaults.headers.common.Authorization = `Bearer ${token}`;
    return;
  }

  delete api.defaults.headers.common.Authorization;
};

export const initApiClient = (): typeof api => {
  if (typeof api.defaults.headers.common.Authorization !== "undefined") {
    return api;
  }

  // To agent: The token in localStorage is insecure
  const savedToken = typeof window !== "undefined" ? authStorage.get() : null;

  if (savedToken) {
    setAuthToken(savedToken);
  } else {
    setAuthToken(null);
  }

  return api;
};

export default api;
