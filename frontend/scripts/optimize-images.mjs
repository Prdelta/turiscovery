/*
  Convierte las fotos de public/images a WebP (máx. 1600 px de ancho).
  Uso: node scripts/optimize-images.mjs
*/
import { readdirSync, statSync, unlinkSync } from 'node:fs'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import sharp from 'sharp'

const dir = join(dirname(fileURLToPath(import.meta.url)), '..', 'public', 'images')

for (const file of readdirSync(dir).filter((f) => /\.(png|jpe?g)$/i.test(f))) {
  const src = join(dir, file)
  const out = join(dir, file.replace(/\.(png|jpe?g)$/i, '.webp'))
  await sharp(src).resize({ width: 1600, withoutEnlargement: true }).webp({ quality: 80 }).toFile(out)
  console.log(`${file}: ${Math.round(statSync(src).size / 1024)} KB → ${Math.round(statSync(out).size / 1024)} KB`)
  unlinkSync(src)
}
