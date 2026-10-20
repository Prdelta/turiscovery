import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Pencil, Plus, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { ImageUploader } from '@/shared/ui/ImageUploader'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button } from '@/shared/ui/Button'
import { Alert, Badge, EmptyState, Spinner, StatusBadge } from '@/shared/ui/Feedback'
import { Checkbox, SelectField, TextArea, TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { Danza } from '@/shared/api/types'
import { candelariaKeys, candelariaQueries } from '@/features/candelaria/api'

const EMPTY_DANZA = { name: '', type: 'mestiza', description: '', history: '', characteristics: '', region: '', image_url: '', video_url: '', order: '0', is_featured: false, is_active: true }

export function DanzasAdmin() {
  useDocumentTitle('Danzas')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery(candelariaQueries.admin.danzas())
  const [editing, setEditing] = useState<Danza | 'new' | null>(null)
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/candelaria-danzas/${id}`),
    onSuccess: () => { void qc.invalidateQueries({ queryKey: candelariaKeys.danzas() }); notify('Danza eliminada') },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (editing) return <DanzaForm danza={editing === 'new' ? undefined : editing} onDone={() => setEditing(null)} />

  return (
    <>
      <PageHeader title="Danzas de la Candelaria" action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Nueva danza</Button>} />
      {isLoading ? <Spinner /> : data?.length ? (
        <ul className="flex flex-col gap-2">
          {data.map((d) => (
            <li key={d.id} className={clsx('flex flex-wrap items-center gap-4 card p-4', !d.is_active && 'border-dashed')}>
              <img src={d.image_url || '/images/puno_dancers.webp'} alt="" className="size-14 rounded-[var(--radius-control)] border-2 border-ink object-cover" />
              <div className="min-w-0 flex-1">
                <p className="font-bold">{d.name}</p>
                <p className="text-sm text-ink-soft">{d.type === 'autoctona' ? 'Autóctona' : 'Mestiza'}{d.region ? ` · ${d.region}` : ''} · orden {d.order ?? 0}</p>
              </div>
              {d.is_featured && <Badge className="border-2 border-sun-dark bg-sun text-ink">Destacada</Badge>}
              {!d.is_active && <StatusBadge status="hidden" label="Oculta" />}
              <button onClick={() => setEditing(d)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></button>
              <button onClick={() => confirm(`¿Eliminar “${d.name}”?`) && remove.mutate(d.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"><Trash2 className="size-4.5" /></button>
            </li>
          ))}
        </ul>
      ) : <EmptyState title="No hay danzas registradas" />}
    </>
  )
}

function DanzaForm({ danza, onDone }: { danza?: Danza; onDone: () => void }) {
  const qc = useQueryClient()
  const notify = useToast()
  const [form, setForm] = useState(() => (danza ? {
    ...EMPTY_DANZA,
    ...Object.fromEntries(Object.entries(danza).map(([k, v]) => [k, v ?? ''])),
    order: String(danza.order ?? 0),
  } : EMPTY_DANZA) as typeof EMPTY_DANZA)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, order: Number(form.order) || 0, image_url: form.image_url || null, video_url: form.video_url || null }
      return danza ? api.put(`/candelaria-danzas/${danza.id}`, body) : api.post('/candelaria-danzas', body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: candelariaKeys.danzas() })
      notify(danza ? 'Danza actualizada' : 'Danza creada')
      onDone()
    },
    onError: (e) => { const p = parseApiError(e); setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message }) },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))

  return (
    <>
      <PageHeader title={danza ? `Editar: ${danza.name}` : 'Nueva danza'} />
      <form onSubmit={(e) => { e.preventDefault(); setErrors({}); save.mutate() }} className="grid max-w-3xl gap-5 card p-6 sm:grid-cols-2">
        <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required />
        <SelectField label="Tipo" options={{ autoctona: 'Autóctona', mestiza: 'Mestiza' }} value={form.type} onChange={set('type')} required />
        <TextField label="Región o comunidad" value={form.region} onChange={set('region')} error={errors.region} />
        <TextField label="Orden" type="number" min={0} value={form.order} onChange={set('order')} />
        <TextArea label="Descripción" value={form.description} onChange={set('description')} error={errors.description} required wrapClassName="sm:col-span-2" />
        <TextArea label="Historia" value={form.history} onChange={set('history')} wrapClassName="sm:col-span-2" />
        <TextArea label="Características" value={form.characteristics} onChange={set('characteristics')} wrapClassName="sm:col-span-2" />
        <TextField label="Video (YouTube o Vimeo)" type="url" value={form.video_url} onChange={set('video_url')} error={errors.video_url} wrapClassName="sm:col-span-2" />
        <div className="sm:col-span-2">
          <ImageUploader value={form.image_url ? [form.image_url] : []} onChange={(urls) => setForm((f) => ({ ...f, image_url: urls[0] ?? '' }))} folder="candelaria/danzas" multiple={false} label="Foto" />
        </div>
        <Checkbox label="Destacada" checked={form.is_featured} onChange={(e) => setForm((f) => ({ ...f, is_featured: e.target.checked }))} />
        <Checkbox label="Visible en el sitio" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} />
        {errors._ && <div className="sm:col-span-2"><Alert tone="error">{errors._}</Alert></div>}
        <div className="flex gap-3 sm:col-span-2">
          <Button type="submit" loading={save.isPending}>{danza ? 'Guardar cambios' : 'Crear danza'}</Button>
          <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
        </div>
      </form>
    </>
  )
}
