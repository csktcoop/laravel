import { fileURLToPath, URL } from "node:url";
import vue from "@vitejs/plugin-vue";
import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  if (!env.VITE_API_URL) {
    throw new Error("VITE_API_URL is not defined.");
  }

  return {
    plugins: [vue()],
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("./src", import.meta.url)),
        "@domains": fileURLToPath(new URL("./src/domains", import.meta.url)),
        "@shared": fileURLToPath(new URL("./src/shared", import.meta.url)),
      },
    },
    server: {
      strictPort: true,
      proxy: {
        "/api": {
          target: env.VITE_API_URL,
          changeOrigin: true,
        },
      },
    },
  };
});
