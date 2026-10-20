import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Eye, EyeOff, Pencil, Plus, Search, Trash2 } from 'lucide-react'
import { lazy, Suspense, useState, type FormEvent } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { useAuth } from '../../auth/AuthContext'
import { ImageUploader } from '../../components/ImageUploader'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Button, ButtonLink } from '../../components/ui/Button'
import { TypeTag } from '../../components/textile/Tags'
import { StitchRow, Tocapu } from '../../components/textile/Weave'
import { Alert, CardGridSkeleton, EmptyState, ErrorState, Pagination, Spinner, StatusBadge } from '../../components/ui/Feedback'
import { ChipGroup, IconAction, PanelCard, StatTile, TEXT_LINK } from '../../components/ui/Panel'
import { Checkbox, SelectField, TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError, type Paginated } from '../../lib/api'
import { CONTENT, imageOf, titleOf, type ContentType } from '../../lib/content'
import { formatDate, formatPrice, toDateInput, toDateTimeInput } from '../../lib/format'
import { useDebounced, useDocumentTitle } from '../../lib/hooks'
import type { Anfitrion, AnyContent, Booking, Locale, User } from '../../lib/types'
import { FORM_CONFIG, type FieldDef } from './contentFields'

const LocationPicker = lazy(() => import('../../components/Map').then((m) => ({ default: m.LocationPicker })))

const MANAGED: ContentType[] = ['locales', 'experiencias', 'eventos', 'promociones', 'candelaria']

/* ---------- Resumen ---------- */

export function DashboardHome() {
  useDocumentTitle('Panel de socio')
  const { user } = useAuth()
  const stats = useQuery({ queryKey: ['stats'], queryFn: () => getData<Record<string, number>>('/stats') })
  const recent = useQuery({
    queryKey: ['recent-content'],
    queryFn: () => getData<(AnyContent & { contentType: ContentType })[]>('/recent-content', { limit: 4 }),
  })
  const bookings = useQuery({
    queryKey: ['partner-bookings', 'pending', 1],
    queryFn: () => getData<Paginated<Booking & { user?: User }>>('/partner/bookings', { status: 'pending' }),
  })

  return (
    <>
      <PageHeader
        title={user?.role === 'admin' ? 'Contenido de la plataforma' : 'Tu negocio en Turiscovery'}
        description={user?.role === 'admin' ? 'Como administrador ves y gestionas el contenido de todos los socios.' : 'Publica y actualiza lo que ofreces. Los cambios se ven en el sitio en unos minutos.'}
      />
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {(['locales', 'experiencias', 'eventos', 'promociones'] as const).map((t) => (
          <StatTile key={t} to={`/dashboard/${t}`} value={stats.data?.[t] ?? '–'} label={CONTENT[t].plural} motif={CONTENT[t].motif} dye={CONTENT[t].dye} />
        ))}
      </div>

      <section className="mt-8 grid gap-6 xl:grid-cols-2">
        <PanelCard
          title={
            <span className="flex items-center gap-2">
              Reservas por confirmar
              {bookings.data?.total ? <span className="rounded-full bg-sun px-2 text-sm font-extrabold">{bookings.data.total}</span> : null}
            </span>
          }
          action={<Link to="/dashboard/reservas" className={TEXT_LINK}>Ver todas</Link>}
          bodyClassName="py-2"
        >
          {bookings.isLoading ? (
            <div className="skeleton my-3 h-24" />
          ) : bookings.data?.data.length ? (
            <ul className="flex flex-col divide-y-2 divide-line">
              {bookings.data.data.slice(0, 5).map((b) => (
                <li key={b.id} className="flex items-center gap-3 py-3">
                  <Tocapu motif="olas" dye="anil" size={24} />
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-bold">{b.experiencia?.title}</p>
                    <p className="text-sm text-ink-soft">{b.user?.name} · {formatDate(b.booking_date)} · {b.participants} pers.</p>
                  </div>
                  <StatusBadge status="pending" />
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-3 text-ink-soft">No tienes reservas pendientes. Te avisamos aquí cuando llegue una.</p>
          )}
        </PanelCard>
        <PanelCard title="Publicado recientemente" bodyClassName="py-2">
          {recent.isLoading ? (
            <div className="skeleton my-3 h-24" />
          ) : recent.data?.length ? (
            <ul className="flex flex-col divide-y-2 divide-line">
              {recent.data.slice(0, 6).map((item) => (
                <li key={`${item.contentType}-${item.id}`} className="flex items-center gap-3 py-3">
                  <img src={imageOf(item, item.contentType)} alt="" className="size-12 rounded-[var(--radius-chip)] border-2 border-ink object-cover" />
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-bold">{titleOf(item as never)}</p>
                    <p className="text-sm text-ink-soft">{CONTENT[item.contentType]?.singular} · {formatDate(item.created_at)}</p>
                  </div>
                  <IconAction label="Editar" to={`/dashboard/${item.contentType}/${item.id}`}>
                    <Pencil className="size-4.5" />
                  </IconAction>
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-3 text-ink-soft">Aún no publicas nada. Empieza registrando tu local.</p>
          )}
        </PanelCard>
      </section>

      <StitchRow dye="anil" className="mt-10" />
      <section aria-labelledby="crear" className="mt-8">
        <h2 id="crear" className="t-h3 mb-4">Publicar algo nuevo</h2>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
          {MANAGED.map((t) => (
            <Link key={t} to={`/dashboard/${t}/nuevo`} className="card flex min-h-14 items-center gap-3 p-3 font-bold transition-colors hover:bg-sky">
              <Tocapu motif={CONTENT[t].motif} dye={CONTENT[t].dye} size={28} />
              <span className="flex-1">{CONTENT[t].singular}</span>
              <Plus className="size-4.5 text-ink-soft" aria-hidden />
            </Link>
          ))}
        </div>
      </section>
    </>
  )
}

/* ---------- Listado de contenido propio ---------- */

export function ContentManager() {
  const { type } = useParams() as { type: ContentType }
  const meta = CONTENT[type]
  useDocumentTitle(`Mis ${meta.plural.toLowerCase()}`)
  const { user } = useAuth()
  const qc = useQueryClient()
  const notify = useToast()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const debounced = useDebounced(search)

  const list = useQuery({
    queryKey: ['my', type, page, debounced],
    queryFn: () => getData<Paginated<AnyContent>>(`/my/${type}`, { page, search: debounced, per_page: 15 }),
  })

  const invalidate = () => {
    void qc.invalidateQueries({ queryKey: ['my', type] })
    void qc.invalidateQueries({ queryKey: ['stats'] })
    void qc.invalidateQueries({ queryKey: ['recent-content'] })
    void qc.invalidateQueries({ queryKey: ['list', type] })
  }

  const toggle = useMutation({
    mutationFn: (item: AnyContent) => api.put(`/${type}/${item.id}`, { is_active: !item.is_active }),
    onSuccess: (_, item) => {
      invalidate()
      notify(item.is_active ? 'Ocultado del sitio público' : 'Publicado en el sitio')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/${type}/${id}`),
    onSuccess: () => {
      invalidate()
      notify(`${meta.singular} eliminado`)
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (!MANAGED.includes(type)) return <EmptyState title="Sección no encontrada" />

  return (
    <>
      <PageHeader
        title={meta.plural}
        description={user?.role === 'admin' ? 'Contenido de todos los socios.' : undefined}
        action={<ButtonLink to={`/dashboard/${type}/nuevo`} icon={<Plus className="size-4.5" />}>Nuevo</ButtonLink>}
      />
      <div className="relative mb-6 max-w-md">
        <label htmlFor="mine-search" className="sr-only">Buscar</label>
        <Search className="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-ink-soft" aria-hidden />
        <input id="mine-search" type="search" value={search} onChange={(e) => { setSearch(e.target.value); setPage(1) }} placeholder="Buscar por título" className="field-input pl-12" />
      </div>
      {list.isLoading ? (
        <CardGridSkeleton count={3} />
      ) : list.isError ? (
        <ErrorState message={parseApiError(list.error).message} onRetry={() => list.refetch()} />
      ) : list.data?.data.length ? (
        <>
          <ul className="flex flex-col gap-3">
            {list.data.data.map((item) => (
              <li key={item.id} className={clsx('card flex flex-wrap items-center gap-4 p-3 pr-4', !item.is_active && 'border-dashed')}>
                <img
                  src={imageOf(item, type)}
                  alt=""
                  className={clsx('size-16 rounded-[var(--radius-control)] border-2 border-ink object-cover', !item.is_active && 'grayscale')}
                />
                <div className="min-w-0 flex-1">
                  <p className="truncate font-bold">{titleOf(item as never)}</p>
                  <p className="text-sm text-ink-soft">
                    Creado el {formatDate(item.created_at)}
                    {user?.role === 'admin' && item.user ? ` · ${item.user.name}` : ''}
                  </p>
                </div>
                <StatusBadge status={item.is_active ? 'published' : 'hidden'} />
                <div className="flex gap-2">
                  <IconAction label={item.is_active ? 'Ocultar del sitio' : 'Publicar'} onClick={() => toggle.mutate(item)} disabled={toggle.isPending && toggle.variables?.id === item.id}>
                    {item.is_active ? <EyeOff className="size-4.5" /> : <Eye className="size-4.5" />}
                  </IconAction>
                  <IconAction label="Editar" to={`/dashboard/${type}/${item.id}`}>
                    <Pencil className="size-4.5" />
                  </IconAction>
                  <IconAction label="Eliminar" danger onClick={() => confirm(`¿Eliminar “${titleOf(item as never)}”? No se puede deshacer.`) && remove.mutate(item.id)}>
                    <Trash2 className="size-4.5" />
                  </IconAction>
                </div>
              </li>
            ))}
          </ul>
          <Pagination page={list.data.current_page} lastPage={list.data.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState
          title={`No tienes ${meta.plural.toLowerCase()}`}
          icon={<Tocapu motif={meta.motif} dye={meta.dye} size={40} framed={false} />}
          action={<ButtonLink to={`/dashboard/${type}/nuevo`}>Crear {meta.singular.toLowerCase()}</ButtonLink>}>
          {type === 'promociones' ? 'Las promociones se asocian a un local: regístralo primero si aún no lo tienes.' : 'Lo que publiques aparecerá en el sitio público.'}
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Formulario crear / editar ---------- */

type Values = Record<string, unknown>

function toFormValues(type: ContentType, item: AnyContent | undefined): Values {
  const config = FORM_CONFIG[type]
  const v: Values = {}
  for (const f of config.fields) {
    const raw = (item as unknown as Values | undefined)?.[f.name]
    if (f.kind === 'datetime') v[f.name] = toDateTimeInput(raw as string)
    else if (f.kind === 'date') v[f.name] = toDateInput(raw as string)
    else if (f.kind === 'tags') v[f.name] = Array.isArray(raw) ? raw.join(', ') : ''
    else if (f.kind === 'checkbox') v[f.name] = !!raw
    else if (f.kind === 'time') v[f.name] = typeof raw === 'string' ? raw.slice(0, 5) : ''
    else v[f.name] = raw ?? ''
  }
  const located = item as { latitude?: number | null; longitude?: number | null } | undefined
  v.latitude = located?.latitude ?? null
  v.longitude = located?.longitude ?? null
  v.images = item?.images ?? []
  if (!item && type === 'promociones') v.discount_type = 'percentage'
  if (!item && type === 'experiencias') v.difficulty = 'medium'
  return v
}

function toPayload(type: ContentType, values: Values, isEdit: boolean) {
  const config = FORM_CONFIG[type]
  const body: Values = { images: values.images }
  for (const f of config.fields) {
    if (f.when && !f.when(values)) continue
    const raw = values[f.name]
    if (f.kind === 'tags') {
      body[f.name] = String(raw ?? '').split(',').map((t) => t.trim()).filter(Boolean)
    } else if (f.kind === 'number') {
      body[f.name] = raw === '' || raw === null ? null : Number(raw)
    } else if (f.kind === 'checkbox') {
      body[f.name] = !!raw
    } else if (f.kind === 'datetime') {
      body[f.name] = raw ? new Date(String(raw)).toISOString() : null
    } else if (f.kind === 'locale' || f.kind === 'anfitrion') {
      body[f.name] = raw ? Number(raw) : null
    } else {
      const s = String(raw ?? '').trim()
      // En edición se envían vacíos como null para poder borrar campos opcionales
      if (s || isEdit) body[f.name] = s || null
    }
  }
  if (config.location && values.latitude && values.longitude) {
    body.latitude = values.latitude
    body.longitude = values.longitude
  }
  return body
}

export function ContentFormPage() {
  const { type, id } = useParams() as { type: ContentType; id?: string }
  const isEdit = !!id && id !== 'nuevo'
  const meta = CONTENT[type]
  const config = FORM_CONFIG[type]
  useDocumentTitle(isEdit ? `Editar ${meta.singular.toLowerCase()}` : `Nuevo: ${meta.singular.toLowerCase()}`)

  const item = useQuery({
    queryKey: ['item', type, id],
    queryFn: () => getData<AnyContent>(`/${type}/${id}`),
    enabled: isEdit,
  })

  if (!config) return <EmptyState title="Sección no encontrada" />
  if (isEdit && item.isLoading) return <Spinner />
  if (isEdit && item.isError) return <ErrorState message={parseApiError(item.error).message} onRetry={() => item.refetch()} />

  return <ContentForm key={id ?? 'nuevo'} type={type} item={isEdit ? item.data : undefined} />
}

function ContentForm({ type, item }: { type: ContentType; item?: AnyContent }) {
  const meta = CONTENT[type]
  const config = FORM_CONFIG[type]
  const navigate = useNavigate()
  const qc = useQueryClient()
  const notify = useToast()
  const [values, setValues] = useState<Values>(() => toFormValues(type, item))
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [formError, setFormError] = useState<string | null>(null)

  const needsLocales = config.fields.some((f) => f.kind === 'locale')
  const locales = useQuery({
    queryKey: ['my', 'locales', 'all'],
    queryFn: () => getData<Paginated<Locale>>('/my/locales', { per_page: 100 }),
    enabled: needsLocales,
  })

  const needsHosts = config.fields.some((f) => f.kind === 'anfitrion')
  const hosts = useQuery({
    queryKey: ['my-anfitriones'],
    queryFn: () => getData<Anfitrion[]>('/my-anfitriones'),
    enabled: needsHosts,
  })

  const save = useMutation({
    mutationFn: () => {
      const body = toPayload(type, values, !!item)
      return item ? api.put(`/${type}/${item.id}`, body) : api.post(`/${type}`, body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['my', type] })
      void qc.invalidateQueries({ queryKey: ['list', type] })
      void qc.invalidateQueries({ queryKey: ['item', type] })
      void qc.invalidateQueries({ queryKey: ['stats'] })
      void qc.invalidateQueries({ queryKey: ['recent-content'] })
      notify(item ? 'Cambios guardados' : `${meta.singular} publicado`)
      navigate(`/dashboard/${type}`)
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(p.fields)
      setFormError(p.message)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
  })

  const set = (name: string, value: unknown) => setValues((v) => ({ ...v, [name]: value }))

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    setErrors({})
    setFormError(null)
    if (config.location === 'required' && !(values.latitude && values.longitude)) {
      setErrors({ latitude: 'Marca la ubicación en el mapa.' })
      setFormError('Marca la ubicación en el mapa.')
      return
    }
    save.mutate()
  }

  const localeOptions = Object.fromEntries((locales.data?.data ?? []).map((l) => [String(l.id), l.name]))
  const hostOptions = Object.fromEntries((hosts.data ?? []).map((h) => [String(h.id), h.community ? `${h.name} (${h.community})` : h.name]))

  return (
    <>
      <div className="mb-3">
        <TypeTag type={type} />
      </div>
      <PageHeader title={item ? `Editar: ${titleOf(item as never)}` : `Nuevo: ${meta.singular.toLowerCase()}`} />
      <form onSubmit={onSubmit} className="flex max-w-4xl flex-col gap-6" noValidate>
        {formError && <Alert tone="error">{formError}</Alert>}
        <PanelCard title="Información" bodyClassName="grid gap-5 sm:grid-cols-2">
          {config.fields.map((f) =>
            f.when && !f.when(values) ? null : (
              <FieldInput
                key={f.name}
                field={f}
                value={values[f.name]}
                error={errors[f.name]}
                onChange={(v) => set(f.name, v)}
                localeOptions={f.kind === 'anfitrion' ? hostOptions : localeOptions}
                localesLoading={f.kind === 'anfitrion' ? hosts.isLoading : locales.isLoading}
              />
            ),
          )}
        </PanelCard>

        {config.location && (
          <PanelCard
            title={
              <>
                Ubicación en el mapa{config.location === 'required' && <span className="text-cochinilla"> *</span>}
              </>
            }
            bodyClassName="flex flex-col gap-3"
          >
            <p className="text-sm text-ink-soft">Haz clic en el mapa para marcar el punto. Puedes arrastrar el marcador para ajustarlo.</p>
            <Suspense fallback={<div className="skeleton h-72" />}>
              <LocationPicker
                lat={values.latitude as number | null}
                lng={values.longitude as number | null}
                onChange={(lat, lng) => setValues((v) => ({ ...v, latitude: lat, longitude: lng }))}
              />
            </Suspense>
            {values.latitude ? (
              <p className="text-sm text-ink-soft">Coordenadas: {String(values.latitude)}, {String(values.longitude)}</p>
            ) : null}
            {(errors.latitude || errors.longitude) && <p className="text-sm font-bold text-cochinilla">{errors.latitude || errors.longitude}</p>}
          </PanelCard>
        )}

        <PanelCard title="Fotos">
          <ImageUploader value={(values.images as string[]) ?? []} onChange={(urls) => set('images', urls)} folder={config.uploadFolder} label="La primera es la portada" />
        </PanelCard>

        {/* Acciones fijas al pie: siempre a mano en formularios largos (sobre la barra inferior en móvil) */}
        <div className="sticky bottom-20 z-10 flex flex-wrap items-center gap-3 rounded-[var(--radius-panel)] border-2 border-ink bg-white p-3 lg:bottom-4">
          <Button type="submit" loading={save.isPending} loadingText="Guardando…">{item ? 'Guardar cambios' : 'Publicar'}</Button>
          <ButtonLink to={`/dashboard/${type}`} variant="secondary">Cancelar</ButtonLink>
          {item && (
            <Link to={`${meta.path}/${item.id}`} target="_blank" className={clsx(TEXT_LINK, 'ml-auto px-2')}>
              Ver en el sitio
            </Link>
          )}
        </div>
      </form>
    </>
  )
}

function FieldInput({
  field: f,
  value,
  error,
  onChange,
  localeOptions,
  localesLoading,
}: {
  field: FieldDef
  value: unknown
  error?: string
  onChange: (v: unknown) => void
  localeOptions: Record<string, string>
  localesLoading: boolean
}) {
  const wrap = f.wide || f.kind === 'textarea' ? 'sm:col-span-2' : undefined
  const common = { label: f.label, error, hint: f.hint, required: f.required, wrapClassName: wrap }
  switch (f.kind) {
    case 'textarea':
      return <TextArea {...common} value={String(value ?? '')} onChange={(e) => onChange(e.target.value)} />
    case 'select':
      return <SelectField {...common} options={f.options ?? {}} placeholder="Elige una opción" value={String(value ?? '')} onChange={(e) => onChange(e.target.value)} />
    case 'anfitrion':
      return (
        <SelectField
          {...common}
          options={localeOptions}
          placeholder={localesLoading ? 'Cargando anfitriones…' : Object.keys(localeOptions).length ? 'Sin anfitrión' : 'Aún no presentas anfitriones'}
          value={String(value ?? '')}
          onChange={(e) => onChange(e.target.value)}
        />
      )
    case 'locale':
      return (
        <SelectField
          {...common}
          options={localeOptions}
          placeholder={localesLoading ? 'Cargando locales…' : Object.keys(localeOptions).length ? 'Sin local' : 'Primero registra un local'}
          value={String(value ?? '')}
          onChange={(e) => onChange(e.target.value)}
        />
      )
    case 'checkbox':
      return (
        <div className={clsx('flex items-end', wrap)}>
          <Checkbox label={f.label} checked={!!value} onChange={(e) => onChange(e.target.checked)} />
        </div>
      )
    default: {
      const inputType = { text: 'text', number: 'number', date: 'date', datetime: 'datetime-local', url: 'url', email: 'email', tel: 'tel', tags: 'text', time: 'time' }[f.kind]
      return (
        <TextField
          {...common}
          type={inputType}
          min={f.min}
          max={f.max}
          step={f.step}
          value={String(value ?? '')}
          onChange={(e) => onChange(e.target.value)}
        />
      )
    }
  }
}

/* ---------- Reservas recibidas ---------- */

export function PartnerBookingsPage() {
  useDocumentTitle('Reservas recibidas')
  const qc = useQueryClient()
  const notify = useToast()
  const [status, setStatus] = useState('pending')
  const [page, setPage] = useState(1)
  const { data, isLoading } = useQuery({
    queryKey: ['partner-bookings', status, page],
    queryFn: () => getData<Paginated<Booking & { user?: User; contact_phone?: string; contact_email?: string }>>('/partner/bookings', { status: status || undefined, page }),
  })
  const update = useMutation({
    mutationFn: ({ id, status }: { id: number; status: string }) => api.put(`/partner/bookings/${id}`, { status }),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['partner-bookings'] })
      notify('Reserva actualizada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const busy = (id: number, s: string) => update.isPending && update.variables?.id === id && update.variables.status === s

  return (
    <>
      <PageHeader title="Reservas recibidas" description="Confirma o rechaza las reservas de tus experiencias. El viajero ve el estado en su cuenta." />
      <ChipGroup
        label="Estado"
        className="mb-6"
        value={status}
        onChange={(v) => { setStatus(v); setPage(1) }}
        options={[['pending', 'Pendientes'], ['confirmed', 'Confirmadas'], ['completed', 'Completadas'], ['cancelled', 'Canceladas'], ['', 'Todas']]}
      />
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : data?.data.length ? (
        <>
          <ul className="flex flex-col gap-3">
            {data.data.map((b) => (
              <li key={b.id} className="card flex flex-wrap items-center gap-4 p-5">
                <div className="min-w-0 flex-1">
                  <div className="mb-1 flex flex-wrap items-center gap-2">
                    <StatusBadge status={b.status} />
                    <p className="font-bold">{b.experiencia?.title}</p>
                  </div>
                  <p className="text-sm text-ink-soft">
                    {formatDate(b.booking_date)} · {b.participants} persona{b.participants > 1 ? 's' : ''}
                    {Number(b.total_price) > 0 && ` · ${formatPrice(b.total_price)}`}
                  </p>
                  <p className="text-sm">
                    {b.user?.name}
                    {(b.contact_phone || b.user?.phone) && <> · <a href={`tel:${b.contact_phone || b.user?.phone}`} className={TEXT_LINK}>{b.contact_phone || b.user?.phone}</a></>}
                    {(b.contact_email || b.user?.email) && <> · <a href={`mailto:${b.contact_email || b.user?.email}`} className={TEXT_LINK}>{b.contact_email || b.user?.email}</a></>}
                  </p>
                  {b.notes && <p className="mt-1 text-sm text-ink-soft">“{b.notes}”</p>}
                </div>
                <div className="flex flex-wrap gap-2">
                  {b.status === 'pending' && (
                    <>
                      <Button size="sm" loading={busy(b.id, 'confirmed')} loadingText="Confirmando…" onClick={() => update.mutate({ id: b.id, status: 'confirmed' })}>
                        Confirmar
                      </Button>
                      <Button
                        size="sm"
                        variant="secondary"
                        loading={busy(b.id, 'cancelled')}
                        loadingText="Rechazando…"
                        onClick={() => confirm('¿Rechazar esta reserva?') && update.mutate({ id: b.id, status: 'cancelled' })}
                      >
                        Rechazar
                      </Button>
                    </>
                  )}
                  {b.status === 'confirmed' && (
                    <Button size="sm" variant="secondary" loading={busy(b.id, 'completed')} loadingText="Guardando…" onClick={() => update.mutate({ id: b.id, status: 'completed' })}>
                      Marcar completada
                    </Button>
                  )}
                </div>
              </li>
            ))}
          </ul>
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState title="No hay reservas en este estado" icon={<Tocapu motif="olas" dye="anil" size={40} framed={false} />}>
          Cuando un viajero reserve una de tus experiencias, aparecerá aquí para que la confirmes.
        </EmptyState>
      )}
    </>
  )
}
