import { ImagePlus, LoaderCircle, X } from 'lucide-react'
import { useRef, useState } from 'react'
import { api, parseApiError } from '@/shared/api/client'

const MAX_BYTES = 5 * 1024 * 1024
const ACCEPT = 'image/jpeg,image/png,image/webp,image/gif'

/** Reduce imágenes grandes en el navegador antes de subirlas (máx. 1920 px, WebP) */
async function compress(file: File): Promise<File> {
  if (file.type === 'image/gif') return file
  const bitmap = await createImageBitmap(file).catch(() => null)
  if (!bitmap) return file
  const scale = Math.min(1, 1920 / Math.max(bitmap.width, bitmap.height))
  if (scale === 1 && file.size < 900_000) return file
  const canvas = document.createElement('canvas')
  canvas.width = Math.round(bitmap.width * scale)
  canvas.height = Math.round(bitmap.height * scale)
  canvas.getContext('2d')?.drawImage(bitmap, 0, 0, canvas.width, canvas.height)
  const blob = await new Promise<Blob | null>((r) => canvas.toBlob(r, 'image/webp', 0.85))
  if (!blob || blob.size >= file.size) return file
  return new File([blob], file.name.replace(/\.\w+$/, '.webp'), { type: 'image/webp' })
}

export function ImageUploader({
  value,
  onChange,
  folder,
  multiple = true,
  label = 'Imágenes',
}: {
  value: string[]
  onChange: (urls: string[]) => void
  folder: string
  multiple?: boolean
  label?: string
}) {
  const input = useRef<HTMLInputElement>(null)
  const [uploading, setUploading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const upload = async (files: FileList | null) => {
    if (!files?.length) return
    setError(null)
    setUploading(true)
    const urls: string[] = []
    try {
      for (const original of Array.from(files)) {
        const file = await compress(original)
        if (file.size > MAX_BYTES) {
          setError(`"${original.name}" pesa más de 5 MB. Usa una imagen más liviana.`)
          continue
        }
        const form = new FormData()
        form.append('image', file)
        form.append('folder', folder)
        const res = await api.post<{ data: { url: string } }>('/uploads', form)
        urls.push(res.data.data.url)
      }
      onChange(multiple ? [...value, ...urls] : urls.slice(0, 1))
    } catch (e) {
      setError(parseApiError(e).message)
    } finally {
      setUploading(false)
      if (input.current) input.current.value = ''
    }
  }

  return (
    <div className="flex flex-col gap-2">
      <span className="text-sm font-semibold">{label}</span>
      <div className="flex flex-wrap gap-3">
        {value.map((url, i) => (
          <div key={url + i} className="group relative size-28 overflow-hidden rounded-[var(--radius-control)] border-2 border-ink bg-white">
            <img src={url} alt="" className="size-full object-cover" />
            {i === 0 && multiple && <span className="absolute bottom-1 left-1 rounded-[var(--radius-chip)] bg-ink px-2 text-xs font-bold text-white">Portada</span>}
            <button
              type="button"
              onClick={() => onChange(value.filter((_, j) => j !== i))}
              className="absolute right-1 top-1 inline-flex size-8 items-center justify-center rounded-[var(--radius-chip)] border-2 border-ink bg-white hover:bg-sky"
              aria-label="Quitar imagen"
            >
              <X className="size-3.5" />
            </button>
          </div>
        ))}
        {(multiple || value.length === 0) && (
          <button
            type="button"
            onClick={() => input.current?.click()}
            disabled={uploading}
            className="flex size-28 flex-col items-center justify-center gap-1 rounded-[var(--radius-control)] border-2 border-dashed border-ink-soft bg-white text-sm font-bold text-ink-soft hover:border-ink hover:text-ink"
          >
            {uploading ? <LoaderCircle className="size-6 animate-spin" /> : <ImagePlus className="size-6" />}
            {uploading ? 'Subiendo…' : 'Añadir'}
          </button>
        )}
      </div>
      <input ref={input} type="file" accept={ACCEPT} multiple={multiple} hidden onChange={(e) => upload(e.target.files)} />
      <p className="text-sm text-ink-soft">JPG, PNG, WebP o GIF de hasta 5 MB. Las fotos grandes se optimizan antes de subir.</p>
      {error && <p className="text-sm font-bold text-cochinilla">{error}</p>}
    </div>
  )
}
