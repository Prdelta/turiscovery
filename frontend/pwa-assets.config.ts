import { defineConfig, minimal2023Preset } from '@vite-pwa/assets-generator/config'

// Genera favicon e íconos de la app instalable a partir de public/icon.svg
export default defineConfig({
  headLinkOptions: { preset: '2023' },
  preset: {
    ...minimal2023Preset,
    maskable: { ...minimal2023Preset.maskable, resizeOptions: { background: '#5930e8' } },
    apple: { ...minimal2023Preset.apple, resizeOptions: { background: '#5930e8' } },
  },
  images: ['public/icon.svg'],
})
