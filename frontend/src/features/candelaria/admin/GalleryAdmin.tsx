import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Pencil, Plus, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { ImageUploader } from '@/shared/ui/ImageUploader'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button } from '@/shared/ui/Button'
import { Alert, EmptyState, Spinner } from '@/shared/ui/Feedback'
import { Checkbox, TextArea, TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle, useNow } from '@/shared/lib/hooks'
import type { GalleryPhoto } from '@/shared/api/types'
import { candelariaKeys, candelariaQueries } from '@/features/candelaria/api'

export function GalleryAdmin() {
  useDocumentTitle('Galería Candelaria')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery(candelariaQueries.admin.gallery())
  const [editing, setEditing] = useState<GalleryPhoto | 'new' | null>(null)
  const invalidate = () => { void qc.invalidateQueries({ queryKey: candelariaKeys.gallery() }) }
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/candelaria-gallery/${id}`),
    onSuccess: () => { invalidate(); notify('Foto eliminada') },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader title="Galería de la Candelaria" action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Añadir foto</Button>} />
      {editing && <PhotoForm photo={editing === 'new' ? undefined : editing} onDone={() => { setEditing(null); invalidate() }} />}
      {isLoading ? <Spinner /> : data?.length ? (
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
          {data.map((p) => (
            <figure key={p.id} className={clsx('overflow-hidden card', !p.is_active && 'border-dashed')}>
              <img src={p.image_url} alt={p.title} className="aspect-[4/3] w-full object-cover" />
              <figcaption className="flex items-start gap-2 p-3">
                <div className="min-w-0 flex-1">
                  <p className="truncate font-semibold">{p.title}</p>
                  <p className="text-sm text-ink-soft">{p.year}{!p.is_active && ' · oculta'}</p>
                </div>
                <button onClick={() => setEditing(p)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4" /></button>
                <button onClick={() => confirm('¿Eliminar esta foto?') && remove.mutate(p.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"><Trash2 className="size-4" /></button>
              </figcaption>
            </figure>
          ))}
        </div>
      ) : <EmptyState title="La galería está vacía" />}
    </>
  )
}

function PhotoForm({ photo, onDone }: { photo?: GalleryPhoto; onDone: () => void }) {
  const currentYear = useNow().getFullYear()
  const notify = useToast()
  const [form, setForm] = useState({
    title: photo?.title ?? '',
    description: photo?.description ?? '',
    image_url: photo?.image_url ?? '',
    year: String(photo?.year ?? currentYear),
    order: String(photo?.order ?? 0),
    is_active: photo?.is_active ?? true,
  })
  const [error, setError] = useState<string | null>(null)
  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, year: Number(form.year), order: Number(form.order) || 0 }
      return photo ? api.put(`/candelaria-gallery/${photo.id}`, body) : api.post('/candelaria-gallery', body)
    },
    onSuccess: () => { notify(photo ? 'Foto actualizada' : 'Foto añadida'); onDone() },
    onError: (e) => setError(parseApiError(e).message),
  })
  return (
    <form
      onSubmit={(e) => {
        e.preventDefault()
        if (!form.image_url) { setError('Sube una imagen.'); return }
        save.mutate()
      }}
      className="mb-8 grid gap-5 rounded-[var(--radius-card)] border-2 border-primary/30 bg-white p-6 sm:grid-cols-2"
    >
      <h2 className="t-h3 sm:col-span-2">{photo ? 'Editar foto' : 'Nueva foto'}</h2>
      <div className="sm:col-span-2">
        <ImageUploader value={form.image_url ? [form.image_url] : []} onChange={(u) => setForm((f) => ({ ...f, image_url: u[0] ?? '' }))} folder="candelaria/gallery" multiple={false} label="Imagen" />
      </div>
      <TextField label="Título" value={form.title} onChange={(e) => setForm((f) => ({ ...f, title: e.target.value }))} required />
      <TextField label="Año" type="number" min={1583} max={currentYear + 1} value={form.year} onChange={(e) => setForm((f) => ({ ...f, year: e.target.value }))} required />
      <TextArea label="Descripción" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} wrapClassName="sm:col-span-2" className="min-h-20" />
      <TextField label="Orden" type="number" min={0} value={form.order} onChange={(e) => setForm((f) => ({ ...f, order: e.target.value }))} />
      <div className="flex items-end"><Checkbox label="Visible en el sitio" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} /></div>
      {error && <div className="sm:col-span-2"><Alert tone="error">{error}</Alert></div>}
      <div className="flex gap-3 sm:col-span-2">
        <Button type="submit" loading={save.isPending}>{photo ? 'Guardar' : 'Añadir a la galería'}</Button>
        <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
      </div>
    </form>
  )
}
