import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig, loadEnv } from 'vite'

// En desarrollo, /api, /sanctum y /storage se reenvían a Laravel para que
// la cookie de sesión de Sanctum viva en el mismo origen que la SPA.
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const backend = env.VITE_BACKEND_URL || 'http://127.0.0.1:8000'
  const proxy = { target: backend, changeOrigin: false }

  return {
    plugins: [react(), tailwindcss()],
    server: {
      port: 5173,
      proxy: {
        '/api': proxy,
        '/sanctum': proxy,
        '/storage': proxy,
      },
    },
  }
})
