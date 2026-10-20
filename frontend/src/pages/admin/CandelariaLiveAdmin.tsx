import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ChevronLeft, Pencil, Play, Plus, Radio, SkipForward, Trash2, Undo2 } from 'lucide-react'
import { lazy, Suspense, useState, type FormEvent } from 'react'
import { Link, useParams } from 'react-router'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Button, ButtonLink } from '../../components/ui/Button'
import { Alert, Badge, EmptyState, Spinner, StatusBadge } from '../../components/ui/Feedback'
import { Checkbox, SelectField, TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError } from '../../lib/api'
import { PROGRAM_TYPES } from '../../lib/content'
import { formatDate, formatTime, toDateTimeInput } from '../../lib/format'
import { useDocumentTitle } from '../../lib/hooks'
import type { Danza, LiveState, Performance, ProgramItem } from '../../lib/types'

const LocationPicker = lazy(() => import('../../components/Map').then((m) => ({ default: m.LocationPicker })))
const RouteEditor = lazy(() => import('../../components/Map').then((m) => ({ default: m.RouteEditor })))

const STATUS: Record<Performance['status'], { label: string; cls: string }> = {
  pending: { label: 'Por pasar', cls: 'border-2 border-ink bg-white text-ink' },
  performing: { label: 'Pasando', cls: 'border-2 border-cochinilla bg-cochinilla text-white' },
  done: { label: 'Pasó', cls: 'border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]' },
  skipped: { label: 'No se presentó', cls: 'border-2 border-dashed border-sun-dark bg-transparent text-[#6b4f00]' },
}

/* ---------- Programa ---------- */

export function ProgramAdmin() {
  useDocumentTitle('Candelaria en vivo')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery({ queryKey: ['admin-program'], queryFn: () => getData<ProgramItem[]>('/admin/candelaria-program') })
  const [editing, setEditing] = useState<ProgramItem | 'new' | null>(null)
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/admin/candelaria-program/${id}`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['admin-program'] })
      notify('Jornada eliminada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (editing) return <ProgramItemForm item={editing === 'new' ? undefined : editing} onDone={() => setEditing(null)} />

  return (
    <>
      <PageHeader
        title="Candelaria en vivo"
        description="Publica el programa de la festividad y, el día del desfile, marca qué conjunto está pasando. Los visitantes lo ven en tiempo real."
        action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Nueva jornada</Button>}
      />
      {isLoading ? (
        <Spinner />
      ) : data?.length ? (
        <ul className="flex flex-col gap-3">
          {data.map((item) => (
            <li key={item.id} className={clsx('flex flex-wrap items-center gap-4 card p-4', !item.is_active && 'border-dashed')}>
              <div className="min-w-0 flex-1">
                <p className="font-bold">{item.title}</p>
                <p className="text-sm text-ink-soft">
                  {PROGRAM_TYPES[item.type]} · {formatDate(item.starts_at)}, {formatTime(item.starts_at)} – {formatTime(item.ends_at)}
                  {item.location_name ? ` · ${item.location_name}` : ''} · {item.performances_count ?? 0} conjuntos
                </p>
              </div>
              {!item.is_active && <StatusBadge status="hidden" label="Oculta" />}
              <ButtonLink to={`/admin/candelaria-vivo/${item.id}`} size="sm" icon={<Radio className="size-4" />}>Controlar desfile</ButtonLink>
              <button onClick={() => setEditing(item)} aria-label="Editar jornada" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></button>
              <button
                onClick={() => confirm(`¿Eliminar “${item.title}” y todos sus conjuntos?`) && remove.mutate(item.id)}
                aria-label="Eliminar jornada"
                className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"
              >
                <Trash2 className="size-4.5" />
              </button>
            </li>
          ))}
        </ul>
      ) : (
        <EmptyState title="Aún no hay jornadas" action={<Button onClick={() => setEditing('new')}>Crear la primera jornada</Button>}>
          Crea las jornadas del programa: misas, procesiones, el concurso de danzas autóctonas y la parada de veneración.
        </EmptyState>
      )}
    </>
  )
}

function ProgramItemForm({ item, onDone }: { item?: ProgramItem; onDone: () => void }) {
  const qc = useQueryClient()
  const notify = useToast()
  const [form, setForm] = useState({
    title: item?.title ?? '',
    type: item?.type ?? 'parada',
    starts_at: toDateTimeInput(item?.starts_at),
    ends_at: toDateTimeInput(item?.ends_at),
    location_name: item?.location_name ?? '',
    description: item?.description ?? '',
    is_active: item?.is_active ?? true,
  })
  const [point, setPoint] = useState<{ lat: number | null; lng: number | null }>({ lat: item?.latitude ?? null, lng: item?.longitude ?? null })
  const [route, setRoute] = useState<[number, number][]>(item?.route ?? [])
  const [errors, setErrors] = useState<Record<string, string>>({})

  const save = useMutation({
    mutationFn: () => {
      const body = {
        ...form,
        starts_at: form.starts_at ? new Date(form.starts_at).toISOString() : null,
        ends_at: form.ends_at ? new Date(form.ends_at).toISOString() : null,
        latitude: point.lat,
        longitude: point.lng,
        route: route.length > 1 ? route : null,
      }
      return item ? api.put(`/admin/candelaria-program/${item.id}`, body) : api.post('/admin/candelaria-program', body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['admin-program'] })
      void qc.invalidateQueries({ queryKey: ['candelaria-program'] })
      notify(item ? 'Jornada actualizada' : 'Jornada creada')
      onDone()
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message })
    },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) =>
    setForm((f) => ({ ...f, [k]: e.target.value }))

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    setErrors({})
    save.mutate()
  }

  return (
    <>
      <PageHeader title={item ? `Editar: ${item.title}` : 'Nueva jornada'} />
      <form onSubmit={onSubmit} className="flex max-w-4xl flex-col gap-6">
        <div className="grid gap-5 card p-6 sm:grid-cols-2">
          <TextField label="Nombre" value={form.title} onChange={set('title')} error={errors.title} required wrapClassName="sm:col-span-2" placeholder="Gran Parada de Veneración" />
          <SelectField label="Tipo" options={PROGRAM_TYPES} value={form.type} onChange={set('type')} required />
          <TextField label="Lugar" value={form.location_name} onChange={set('location_name')} error={errors.location_name} placeholder="Estadio Enrique Torres Belón" />
          <TextField label="Inicio" type="datetime-local" value={form.starts_at} onChange={set('starts_at')} error={errors.starts_at} required />
          <TextField label="Fin" type="datetime-local" value={form.ends_at} onChange={set('ends_at')} error={errors.ends_at} required />
          <TextArea label="Descripción" value={form.description} onChange={set('description')} wrapClassName="sm:col-span-2" className="min-h-20" />
          <Checkbox label="Visible en el programa público" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} />
        </div>
        <div className="flex flex-col gap-3 card p-6">
          <h2 className="t-h3">Punto de encuentro</h2>
          <p className="text-sm text-ink-soft">Opcional. Haz clic en el mapa para marcar el lugar principal.</p>
          <Suspense fallback={<div className="skeleton h-72" />}>
            <LocationPicker lat={point.lat} lng={point.lng} onChange={(lat, lng) => setPoint({ lat, lng })} />
          </Suspense>
        </div>
        <div className="flex flex-col gap-3 card p-6">
          <h2 className="t-h3">Recorrido del desfile</h2>
          <Suspense fallback={<div className="skeleton h-80" />}>
            <RouteEditor value={route} onChange={setRoute} />
          </Suspense>
          {errors.route && <p className="text-sm font-bold text-cochinilla">{errors.route}</p>}
        </div>
        {errors._ && <Alert tone="error">{errors._}</Alert>}
        <div className="flex gap-3">
          <Button type="submit" loading={save.isPending}>{item ? 'Guardar cambios' : 'Crear jornada'}</Button>
          <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
        </div>
      </form>
    </>
  )
}

/* ---------- Control en vivo de una jornada ---------- */

interface ControlData {
  item: ProgramItem
  performances: Performance[]
  state: LiveState
}

export function LiveControl() {
  const { id } = useParams()
  const qc = useQueryClient()
  const notify = useToast()
  const key = ['admin-performances', id]
  const { data, isLoading } = useQuery({
    queryKey: key,
    queryFn: () => getData<ControlData>(`/admin/candelaria-program/${id}/performances`),
    refetchInterval: 15_000,
  })
  useDocumentTitle(data ? `Control: ${data.item.title}` : 'Control del desfile')

  const refresh = () => {
    void qc.invalidateQueries({ queryKey: key })
    void qc.invalidateQueries({ queryKey: ['candelaria-live'] })
  }
  const advance = useMutation({
    mutationFn: () => api.post(`/admin/candelaria-program/${id}/advance`),
    onSuccess: refresh,
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const setStatus = useMutation({
    mutationFn: ({ pid, status }: { pid: number; status: Performance['status'] }) => api.put(`/admin/candelaria-performances/${pid}`, { status }),
    onSuccess: refresh,
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const remove = useMutation({
    mutationFn: (pid: number) => api.delete(`/admin/candelaria-performances/${pid}`),
    onSuccess: refresh,
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (isLoading || !data) return <Spinner />
  const { item, performances, state } = data
  const current = performances.find((p) => p.status === 'performing')
  const next = performances.find((p) => p.status === 'pending')

  return (
    <>
      <Link to="/admin/candelaria-vivo" className="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-ink-soft hover:text-ink">
        <ChevronLeft className="size-4" /> Programa
      </Link>
      <PageHeader
        title={item.title}
        description={`${formatDate(item.starts_at)}, ${formatTime(item.starts_at)} – ${formatTime(item.ends_at)}${item.location_name ? ` · ${item.location_name}` : ''}`}
        action={<ButtonLink to="/candelaria/en-vivo" target="_blank" variant="secondary" size="sm">Ver página pública</ButtonLink>}
      />

      <div className="grid gap-4 rounded-[var(--radius-panel)] border-2 border-ink bg-ink p-6 text-white sm:grid-cols-[1fr_auto] sm:items-center">
        <div aria-live="polite">
          <p className="text-sm font-semibold text-mint">Pasando ahora</p>
          <p className="font-display t-h2 text-white">{current ? `${current.order_number}. ${current.group_name}` : 'Nadie todavía'}</p>
          <p className="mt-1 text-white/75">
            {next ? `Siguiente: ${next.order_number}. ${next.group_name}` : 'No quedan conjuntos por pasar'} · {state.done_count ?? 0} de {state.total ?? 0} ya pasaron · ~{state.minutes_per_group} min por conjunto
          </p>
        </div>
        <Button
          variant="mint"
          size="lg"
          className="h-16 text-lg"
          icon={current ? <SkipForward className="size-6" /> : <Play className="size-6" />}
          disabled={!next && !current}
          loading={advance.isPending}
          onClick={() => advance.mutate()}
        >
          {current ? (next ? 'Pasó, sigue el siguiente' : 'Terminar el último') : 'Empezar desfile'}
        </Button>
      </div>

      <AddPerformances itemId={item.id} onAdded={refresh} />

      <h2 className="mb-3 mt-10 t-h3">Orden de presentación</h2>
      {performances.length ? (
        <ol className="flex flex-col gap-2">
          {performances.map((p) => (
            <li key={p.id} className={clsx('flex flex-wrap items-center gap-3 card p-3', p.status === 'performing' && 'border-cochinilla bg-cochinilla/5')}>
              <span className="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-chip)] border-2 border-ink bg-lana font-display font-extrabold">{p.order_number}</span>
              <div className="min-w-0 flex-1">
                <p className="truncate font-semibold">{p.group_name}</p>
                <p className="truncate text-sm text-ink-soft">
                  {p.danza?.name ?? 'Danza sin asignar'}
                  {p.started_at && ` · ${formatTime(p.started_at)}`}
                  {p.finished_at && ` – ${formatTime(p.finished_at)}`}
                </p>
              </div>
              <Badge className={STATUS[p.status].cls}>{STATUS[p.status].label}</Badge>
              <div className="flex gap-2">
                {p.status !== 'performing' && (
                  <IconBtn label="Marcar como pasando" onClick={() => setStatus.mutate({ pid: p.id, status: 'performing' })}><Play className="size-4" /></IconBtn>
                )}
                {p.status === 'pending' && (
                  <IconBtn label="No se presentó" onClick={() => setStatus.mutate({ pid: p.id, status: 'skipped' })}><SkipForward className="size-4" /></IconBtn>
                )}
                {p.status !== 'pending' && (
                  <IconBtn label="Volver a por pasar" onClick={() => setStatus.mutate({ pid: p.id, status: 'pending' })}><Undo2 className="size-4" /></IconBtn>
                )}
                <IconBtn label="Eliminar" danger onClick={() => confirm(`¿Eliminar a ${p.group_name}?`) && remove.mutate(p.id)}><Trash2 className="size-4" /></IconBtn>
              </div>
            </li>
          ))}
        </ol>
      ) : (
        <EmptyState title="Sin conjuntos todavía">Pega arriba la lista oficial de conjuntos en orden de presentación.</EmptyState>
      )}
    </>
  )
}

function IconBtn({ label, onClick, children, danger }: { label: string; onClick: () => void; children: React.ReactNode; danger?: boolean }) {
  return (
    <button onClick={onClick} aria-label={label} title={label} className={clsx('inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2', danger ? 'border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white' : 'border-ink bg-white text-ink hover:bg-sky')}>
      {children}
    </button>
  )
}

/** Carga masiva: una línea por conjunto, con la danza opcional separada por "|" */
function AddPerformances({ itemId, onAdded }: { itemId: number; onAdded: () => void }) {
  const notify = useToast()
  const [text, setText] = useState('')
  const [unmatched, setUnmatched] = useState<string[]>([])
  const { data: danzas = [] } = useQuery({ queryKey: ['admin-danzas'], queryFn: () => getData<Danza[]>('/admin/candelaria-danzas') })

  const normalize = (s: string) => s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim()

  const add = useMutation({
    mutationFn: (rows: { group_name: string; candelaria_danza_id: number | null }[]) =>
      api.post<{ message: string }>(`/admin/candelaria-program/${itemId}/performances`, { performances: rows }),
    onSuccess: (res) => {
      notify(res.data.message)
      setText('')
      onAdded()
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    const missing: string[] = []
    const rows = text
      .split('\n')
      .map((line) => line.trim())
      .filter(Boolean)
      .map((line) => {
        const [group, danzaName] = line.split('|').map((s) => s.trim())
        const danza = danzaName ? danzas.find((d) => normalize(d.name) === normalize(danzaName)) : undefined
        if (danzaName && !danza) missing.push(danzaName)
        return { group_name: group, candelaria_danza_id: danza?.id ?? null }
      })
    setUnmatched([...new Set(missing)])
    if (rows.length) add.mutate(rows)
  }

  return (
    <form onSubmit={onSubmit} className="mt-6 flex flex-col gap-3 card p-6">
      <TextArea
        label="Agregar conjuntos al final del orden"
        hint="Uno por línea. Para enlazar la danza, agrégala después de una barra: Conjunto Folklórico Juventud Obrera | Diablada"
        value={text}
        onChange={(e) => setText(e.target.value)}
        className="min-h-32 font-mono text-sm"
      />
      {unmatched.length > 0 && (
        <Alert tone="info">No encontramos estas danzas y se cargaron sin danza: {unmatched.join(', ')}. Revisa que el nombre coincida con el de la sección Danzas.</Alert>
      )}
      <div>
        <Button type="submit" loading={add.isPending} disabled={!text.trim()}>Agregar conjuntos</Button>
      </div>
    </form>
  )
}
