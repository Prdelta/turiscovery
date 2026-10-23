import { useMutation, useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { lazy, Suspense, useState, type FormEvent } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { ImageUploader } from '@/shared/ui/ImageUploader'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { TypeTag } from '@/shared/textile/Tags'
import { Alert, EmptyState, ErrorState, Spinner } from '@/shared/ui/Feedback'
import { PanelCard, TEXT_LINK } from '@/shared/ui/Panel'
import { Checkbox, SelectField, TextArea, TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { CONTENT, titleOf, type ContentType } from '@/shared/config/content'
import { toDateInput, toDateTimeInput } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { AnyContent, Locale } from '@/shared/api/types'
import { FORM_CONFIG, type FieldDef } from '@/features/partner/contentFields'
import { catalogQueries } from '@/features/catalog/api'
import { hostQueries } from '@/features/hosts/api'
import { useInvalidateContent } from '@/features/partner/api'

const LocationPicker = lazy(() => import('@/shared/map/Map').then((m) => ({ default: m.LocationPicker })))

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

  const item = useQuery(catalogQueries.item<AnyContent>(type, isEdit ? id : undefined))

  if (!config) return <EmptyState title="Sección no encontrada" />
  if (isEdit && item.isLoading) return <Spinner />
  if (isEdit && item.isError) return <ErrorState message={parseApiError(item.error).message} onRetry={() => item.refetch()} />

  return <ContentForm key={id ?? 'nuevo'} type={type} item={isEdit ? item.data : undefined} />
}

function ContentForm({ type, item }: { type: ContentType; item?: AnyContent }) {
  const meta = CONTENT[type]
  const config = FORM_CONFIG[type]
  const navigate = useNavigate()
  const invalidateContent = useInvalidateContent()
  const notify = useToast()
  const [values, setValues] = useState<Values>(() => toFormValues(type, item))
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [formError, setFormError] = useState<string | null>(null)

  const needsLocales = config.fields.some((f) => f.kind === 'locale')
  const locales = useQuery({ ...catalogQueries.mine<Locale>('locales', { per_page: 100 }), enabled: needsLocales })

  const needsHosts = config.fields.some((f) => f.kind === 'anfitrion')
  const hosts = useQuery({ ...hostQueries.mine(), enabled: needsHosts })

  const save = useMutation({
    mutationFn: () => {
      const body = toPayload(type, values, !!item)
      return item ? api.put(`/${type}/${item.id}`, body) : api.post(`/${type}`, body)
    },
    onSuccess: () => {
      invalidateContent(type)
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
