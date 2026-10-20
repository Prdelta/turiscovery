import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Pencil, Plus, Trash2 } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Button } from '../../components/ui/Button'
import { Alert, Badge, EmptyState, Spinner } from '../../components/ui/Feedback'
import { Checkbox, SelectField, TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError } from '../../lib/api'
import { ALMANAC_KINDS } from '../../lib/almanac'
import { useDocumentTitle } from '../../lib/hooks'

interface Entry {
  id: number
  title: string
  description?: string | null
  kind: 'fiesta' | 'ritual' | 'agricola' | 'civica'
  place_name?: string | null
  province?: string | null
  month: number
  day: number
  duration_days: number
  is_active: boolean
}

const MONTHS = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre']
const KIND_OPTIONS = { fiesta: ALMANAC_KINDS.fiesta, ritual: ALMANAC_KINDS.ritual, agricola: ALMANAC_KINDS.agricola, civica: ALMANAC_KINDS.civica }

export default function AlmanacAdmin() {
  useDocumentTitle('Almanaque')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery({ queryKey: ['admin-almanaque'], queryFn: () => getData<Entry[]>('/admin/almanaque') })
  const [editing, setEditing] = useState<Entry | 'new' | null>(null)
  const invalidate = () => {
    void qc.invalidateQueries({ queryKey: ['admin-almanaque'] })
    void qc.invalidateQueries({ queryKey: ['almanaque'] })
  }
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/admin/almanaque/${id}`),
    onSuccess: () => {
      invalidate()
      notify('Fecha eliminada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader
        title="Almanaque"
        description="Fiestas patronales, rituales y fechas cívicas que se repiten cada año. Aparecen en “Hoy en Puno” y en el almanaque público. Revisa las fechas móviles cada año."
        action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Nueva fecha</Button>}
      />
      {editing && <EntryForm entry={editing === 'new' ? undefined : editing} onDone={() => { setEditing(null); invalidate() }} />}
      {isLoading ? (
        <Spinner />
      ) : data?.length ? (
        <ul className="flex flex-col gap-2">
          {data.map((e) => (
            <li key={e.id} className={`flex flex-wrap items-center gap-4 card p-4 ${e.is_active ? '' : 'border-dashed'}`}>
              <span className="w-28 shrink-0 font-semibold">{e.day} de {MONTHS[e.month - 1]}</span>
              <div className="min-w-0 flex-1">
                <p className="font-bold">{e.title}</p>
                <p className="text-sm text-ink-soft">
                  {[e.place_name, e.province].filter(Boolean).join(' · ')}
                  {e.duration_days > 1 ? ` · ${e.duration_days} días` : ''}
                </p>
              </div>
              <Badge>{ALMANAC_KINDS[e.kind]}</Badge>
              <button onClick={() => setEditing(e)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></button>
              <button onClick={() => confirm(`¿Eliminar “${e.title}”?`) && remove.mutate(e.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"><Trash2 className="size-4.5" /></button>
            </li>
          ))}
        </ul>
      ) : (
        <EmptyState title="El almanaque está vacío">Agrega las fiestas patronales de cada provincia y los rituales del calendario andino.</EmptyState>
      )}
    </>
  )
}

function EntryForm({ entry, onDone }: { entry?: Entry; onDone: () => void }) {
  const notify = useToast()
  const [form, setForm] = useState({
    title: entry?.title ?? '',
    description: entry?.description ?? '',
    kind: entry?.kind ?? 'fiesta',
    place_name: entry?.place_name ?? '',
    province: entry?.province ?? '',
    month: String(entry?.month ?? 1),
    day: String(entry?.day ?? 1),
    duration_days: String(entry?.duration_days ?? 1),
    is_active: entry?.is_active ?? true,
  })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, month: Number(form.month), day: Number(form.day), duration_days: Number(form.duration_days) || 1 }
      return entry ? api.put(`/admin/almanaque/${entry.id}`, body) : api.post('/admin/almanaque', body)
    },
    onSuccess: () => {
      notify(entry ? 'Fecha actualizada' : 'Fecha agregada')
      onDone()
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message })
    },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))
  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    setErrors({})
    save.mutate()
  }

  return (
    <form onSubmit={onSubmit} className="mb-8 grid gap-5 rounded-[var(--radius-card)] border-2 border-qolle/50 bg-white p-6 sm:grid-cols-2">
      <h2 className="t-h3 sm:col-span-2">{entry ? 'Editar fecha' : 'Nueva fecha'}</h2>
      <TextField label="Nombre" value={form.title} onChange={set('title')} error={errors.title} required wrapClassName="sm:col-span-2" placeholder="Fiesta de la Virgen de la Inmaculada" />
      <SelectField label="Tipo" options={KIND_OPTIONS} value={form.kind} onChange={set('kind')} />
      <TextField label="Provincia" value={form.province} onChange={set('province')} placeholder="Chucuito" />
      <TextField label="Lugar" value={form.place_name} onChange={set('place_name')} placeholder="Juli" wrapClassName="sm:col-span-2" />
      <SelectField label="Mes" options={Object.fromEntries(MONTHS.map((m, i) => [String(i + 1), m]))} value={form.month} onChange={set('month')} />
      <div className="grid grid-cols-2 gap-3">
        <TextField label="Día" type="number" min={1} max={31} value={form.day} onChange={set('day')} error={errors.day} required />
        <TextField label="Dura (días)" type="number" min={1} max={60} value={form.duration_days} onChange={set('duration_days')} />
      </div>
      <TextArea label="Descripción" value={form.description} onChange={set('description')} wrapClassName="sm:col-span-2" className="min-h-20" />
      <Checkbox label="Visible" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} />
      {errors._ && <div className="sm:col-span-2"><Alert tone="error">{errors._}</Alert></div>}
      <div className="flex gap-3 sm:col-span-2">
        <Button type="submit" loading={save.isPending}>{entry ? 'Guardar' : 'Agregar'}</Button>
        <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
      </div>
    </form>
  )
}
