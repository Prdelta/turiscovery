import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { defineConfig, loadEnv } from 'vite'
import { VitePWA } from 'vite-plugin-pwa'

// En desarrollo, /api, /sanctum y /storage se reenvían a Laravel para que
// la cookie de sesión de Sanctum viva en el mismo origen que la SPA.
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const backend = env.VITE_BACKEND_URL || 'http://127.0.0.1:8000'
  const proxy = { target: backend, changeOrigin: false }
  const proxies = { '/api': proxy, '/sanctum': proxy, '/storage': proxy }

  return {
    plugins: [
      react(),
      tailwindcss(),
      VitePWA({
        registerType: 'autoUpdate',
        includeAssets: ['favicon.ico', 'apple-touch-icon-180x180.png', 'icon.svg'],
        manifest: {
          name: 'Turiscovery · Puno',
          short_name: 'Turiscovery',
          description: 'Experiencias, eventos, promociones y la Candelaria de Puno. Funciona sin conexión en el lago y las islas.',
          lang: 'es-PE',
          start_url: '/',
          scope: '/',
          display: 'standalone',
          orientation: 'portrait',
          background_color: '#eef2f8',
          theme_color: '#5930e8',
          categories: ['travel', 'navigation'],
          icons: [
            { src: 'pwa-64x64.png', sizes: '64x64', type: 'image/png' },
            { src: 'pwa-192x192.png', sizes: '192x192', type: 'image/png' },
            { src: 'pwa-512x512.png', sizes: '512x512', type: 'image/png' },
            { src: 'maskable-icon-512x512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
          ],
          shortcuts: [
            { name: 'Mis cupones', url: '/cuenta/cupones', icons: [{ src: 'pwa-192x192.png', sizes: '192x192' }] },
            { name: 'Candelaria en vivo', url: '/candelaria/en-vivo', icons: [{ src: 'pwa-192x192.png', sizes: '192x192' }] },
            { name: 'Mi plan de viaje', url: '/planificador', icons: [{ src: 'pwa-192x192.png', sizes: '192x192' }] },
          ],
        },
        workbox: {
          // La app completa (código, estilos, íconos, modelos 3D y fotos optimizadas) queda guardada al instalarse
          globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff2,glb,json}'],
          maximumFileSizeToCacheInBytes: 3 * 1024 * 1024,
          navigateFallback: 'index.html',
          navigateFallbackDenylist: [/^\/api\//, /^\/sanctum\//, /^\/storage\//, /^\/up$/],
          cleanupOutdatedCaches: true,
          runtimeCaching: [
            {
              // Datos de la API: siempre se intenta la red; sin conexión se usa la última respuesta guardada
              urlPattern: ({ url, request }) => url.pathname.startsWith('/api/') && request.method === 'GET' && !url.pathname.startsWith('/api/auth/'),
              handler: 'NetworkFirst',
              options: {
                cacheName: 'turiscovery-api',
                networkTimeoutSeconds: 6,
                expiration: { maxEntries: 400, maxAgeSeconds: 14 * 24 * 60 * 60 },
                cacheableResponse: { statuses: [200] },
              },
            },
            {
              // Fotos que suben los socios
              urlPattern: ({ url }) => url.pathname.startsWith('/storage/'),
              handler: 'CacheFirst',
              options: {
                cacheName: 'turiscovery-fotos',
                expiration: { maxEntries: 300, maxAgeSeconds: 30 * 24 * 60 * 60 },
                cacheableResponse: { statuses: [0, 200] },
              },
            },
            {
              // Mosaicos del mapa que el usuario ya vio (no se descargan zonas completas por política de OpenStreetMap)
              urlPattern: ({ url }) => url.hostname.endsWith('tile.openstreetmap.org'),
              handler: 'CacheFirst',
              options: {
                cacheName: 'turiscovery-mapa',
                expiration: { maxEntries: 600, maxAgeSeconds: 30 * 24 * 60 * 60 },
                cacheableResponse: { statuses: [0, 200] },
              },
            },
            {
              // Clima del lago: sin conexión se muestra el último pronóstico guardado
              urlPattern: ({ url }) => url.hostname === 'api.open-meteo.com',
              handler: 'NetworkFirst',
              options: {
                cacheName: 'turiscovery-clima',
                networkTimeoutSeconds: 5,
                expiration: { maxEntries: 10, maxAgeSeconds: 2 * 24 * 60 * 60 },
                cacheableResponse: { statuses: [200] },
              },
            },
            {
              urlPattern: ({ url }) => url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com',
              handler: 'CacheFirst',
              options: {
                cacheName: 'turiscovery-fuentes',
                expiration: { maxEntries: 20, maxAgeSeconds: 365 * 24 * 60 * 60 },
                cacheableResponse: { statuses: [0, 200] },
              },
            },
          ],
        },
      }),
    ],
    server: { port: 5173, proxy: proxies },
    preview: { port: 4173, proxy: proxies },
  }
})
