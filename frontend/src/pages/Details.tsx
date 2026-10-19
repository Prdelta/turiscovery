import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { CalendarDays, CalendarPlus, ChevronLeft, Clock, Globe, Mail, MapPin, Phone, Share2, Ticket, Users } from 'lucide-react'
import { lazy, Suspense, useState, type FormEvent, type ReactNode } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { useAuth } from '../auth/AuthContext'
import { ContentCard } from '../components/ContentCard'
import { FavoriteButton } from '../components/FavoriteButton'
import { ReviewsSection } from '../components/ReviewsSection'
import { Button, ButtonLink } from '../components/ui/Button'
import { Alert, Badge, ErrorState, Spinner } from '../components/ui/Feedback'
import { TextArea, TextField } from '../components/ui/Field'
import { useToast } from '../components/ui/Toast'
import { api, parseApiError } from '../lib/api'
import { CANDELARIA_CATEGORIES, CONTENT, DIFFICULTY, EVENT_CATEGORIES, LOCALE_CATEGORIES, imageOf, titleOf, type ContentType } from '../lib/content'
import { daysUntil, formatDate, formatPrice, formatTime, googleCalendarUrl, relativeDays, toDateInput } from '../lib/format'
import { useContentItem, useDocumentTitle } from '../lib/hooks'
import type { AnyContent, Candelaria, EventAttendance, Evento, Experiencia, Locale, Promocion, UserCoupon } from '../lib/types'

const MapView = lazy(() => import('../components/Map').then((m) => ({ default: m.MapView })))

/* ---------- Estructura común ---------- */

function useDetail<T extends AnyContent>(type: ContentType) {
  const { id } = useParams()
  const query = useContentItem<T>(type, id)
  useDocumentTitle(query.data ? titleOf(query.data as never) : CONTENT[type].singular)
  return query
}

function DetailState({ isLoading, error, refetch }: { isLoading: boolean; error: unknown; refetch: () => void }) {
  if (isLoading) return <Spinner />
  const status = (error as { response?: { status?: number } })?.response?.status
  return (
    <div className="container-page py-20">
      {status === 404 ? (
        <div className="text-center">
          <h1 className="text-3xl font-bold">No encontramos esta publicación</h1>
          <p className="mt-2 text-ink-soft">Puede que el socio la haya retirado.</p>
          <Link to="/" className="mt-6 inline-block font-semibold text-primary">Volver al inicio</Link>
        </div>
      ) : (
        <ErrorState message={parseApiError(error).message} onRetry={refetch} />
      )}
    </div>
  )
}

function Gallery({ item, type }: { item: AnyContent; type: ContentType }) {
  const images = item.images?.length ? item.images : [imageOf(item, type)]
  const [active, setActive] = useState(0)
  return (
    <div className="flex flex-col gap-3">
      <img src={images[active]} alt="" className="aspect-[16/10] w-full rounded-[var(--radius-card)] bg-line object-cover" />
      {images.length > 1 && (
        <div className="flex gap-2 overflow-x-auto pb-1">
          {images.map((src, i) => (
            <button
              key={src + i}
              onClick={() => setActive(i)}
              aria-label={`Ver imagen ${i + 1}`}
              aria-pressed={i === active}
              className={clsx('size-20 shrink-0 overflow-hidden rounded-xl border-2', i === active ? 'border-primary' : 'border-transparent')}
            >
              <img src={src} alt="" className="size-full object-cover" />
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

function ShareButton({ title }: { title: string }) {
  const notify = useToast()
  const share = async () => {
    const url = window.location.href
    if (navigator.share) {
      await navigator.share({ title, url }).catch(() => {})
    } else {
      await navigator.clipboard.writeText(url)
      notify('Enlace copiado')
    }
  }
  return (
    <button onClick={share} className="inline-flex h-11 items-center gap-2 rounded-full border border-line bg-white px-5 font-semibold hover:border-ink/40">
      <Share2 className="size-4.5" /> Compartir
    </button>
  )
}

function DetailShell({
  item,
  type,
  kicker,
  meta,
  aside,
  children,
  showMap = true,
}: {
  item: AnyContent
  type: ContentType
  kicker?: ReactNode
  meta?: ReactNode
  aside?: ReactNode
  children?: ReactNode
  showMap?: boolean
}) {
  const navigate = useNavigate()
  const title = titleOf(item as never)
  const withCoords = item as { latitude?: number | null; longitude?: number | null; formatted_address?: string | null; address?: string | null }
  const coords = withCoords.latitude && withCoords.longitude ? { latitude: withCoords.latitude, longitude: withCoords.longitude } : null

  return (
    <article className="container-page py-8 sm:py-12">
      <button onClick={() => navigate(-1)} className="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink-soft hover:text-ink">
        <ChevronLeft className="size-4" /> Volver
      </button>
      <div className="grid gap-10 lg:grid-cols-[1fr_380px]">
        <div className="min-w-0">
          <Gallery item={item} type={type} />
          <header className="mt-8">
            {kicker && <div className="mb-3 flex flex-wrap gap-2">{kicker}</div>}
            <h1 className="text-4xl font-extrabold sm:text-5xl">{title}</h1>
            {meta && <div className="mt-4 flex flex-col gap-2 text-ink-soft">{meta}</div>}
            <div className="mt-6 flex flex-wrap gap-3">
              <FavoriteButton type={type} id={item.id} />
              <ShareButton title={title} />
            </div>
          </header>
          <div className="prose-measure mt-8 whitespace-pre-line text-lg leading-relaxed">{item.description}</div>
          {children}
          {showMap && coords?.latitude && coords?.longitude && (
            <section aria-labelledby="ubicacion" className="mt-12">
              <h2 id="ubicacion" className="mb-2 text-2xl font-extrabold">Ubicación</h2>
              {(withCoords.formatted_address || withCoords.address) && <p className="mb-4 text-ink-soft">{withCoords.formatted_address || withCoords.address}</p>}
              <Suspense fallback={<div className="skeleton h-80" />}>
                <MapView points={[{ id: item.id, lat: coords.latitude, lng: coords.longitude, title }]} height={340} />
              </Suspense>
              <a
                href={`https://www.google.com/maps/dir/?api=1&destination=${coords.latitude},${coords.longitude}`}
                target="_blank"
                rel="noreferrer"
                className="mt-3 inline-block font-semibold text-primary"
              >
                Cómo llegar con Google Maps
              </a>
            </section>
          )}
        </div>
        {aside && <aside className="lg:sticky lg:top-24 lg:self-start">{aside}</aside>}
      </div>
      <ReviewsSection type={type} id={item.id} />
    </article>
  )
}

function AsideCard({ children }: { children: ReactNode }) {
  return <div className="flex flex-col gap-5 rounded-[var(--radius-card)] bg-white p-6">{children}</div>
}

function LoginPrompt({ action }: { action: string }) {
  return (
    <ButtonLink to={`/login?next=${encodeURIComponent(location.pathname)}`} className="w-full">
      Inicia sesión para {action}
    </ButtonLink>
  )
}

function LocaleLink({ locale }: { locale?: Locale | null }) {
  if (!locale) return null
  return (
    <p className="flex items-center gap-2">
      <MapPin className="size-4.5" aria-hidden /> Ofrecido por{' '}
      <Link to={`/locales/${locale.id}`} className="font-semibold text-primary">{locale.name}</Link>
    </p>
  )
}

/* ---------- Experiencia ---------- */

export function ExperienciaDetail() {
  const { data, isLoading, error, refetch } = useDetail<Experiencia>('experiencias')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const e = data
  return (
    <DetailShell
      item={e}
      type="experiencias"
      kicker={
        <>
          <Badge className="bg-primary/10 text-primary">Dificultad {DIFFICULTY[e.difficulty]?.toLowerCase()}</Badge>
          {e.tags?.map((t) => <Badge key={t}>{t}</Badge>)}
        </>
      }
      meta={
        <>
          {e.duration_hours && <p className="flex items-center gap-2"><Clock className="size-4.5" aria-hidden /> Duración: {e.duration_hours} horas</p>}
          {e.max_participants && <p className="flex items-center gap-2"><Users className="size-4.5" aria-hidden /> Hasta {e.max_participants} personas por fecha</p>}
          <LocaleLink locale={e.locale} />
        </>
      }
      aside={<BookingCard experiencia={e} />}
    >
      {e.content && (
        <section className="mt-10">
          <h2 className="mb-3 text-2xl font-extrabold">Itinerario y detalles</h2>
          <div className="prose-measure whitespace-pre-line text-ink-soft">{e.content}</div>
        </section>
      )}
    </DetailShell>
  )
}

function BookingCard({ experiencia }: { experiencia: Experiencia }) {
  const { user } = useAuth()
  const notify = useToast()
  const tomorrow = toDateInput(new Date(Date.now() + 86_400_000).toISOString())
  const [date, setDate] = useState(tomorrow)
  const [participants, setParticipants] = useState(1)
  const [phone, setPhone] = useState(user?.phone ?? '')
  const [notes, setNotes] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [done, setDone] = useState(false)
  const price = Number(experiencia.price_pen ?? 0)

  const book = useMutation({
    mutationFn: () =>
      api.post('/bookings', {
        experiencia_id: experiencia.id,
        booking_date: date,
        participants,
        contact_phone: phone || undefined,
        notes: notes || undefined,
      }),
    onSuccess: () => {
      setDone(true)
      setError(null)
      notify('Reserva enviada. El socio te contactará para confirmarla.')
    },
    onError: (e) => setError(parseApiError(e).message),
  })

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    book.mutate()
  }

  return (
    <AsideCard>
      <div>
        <p className="font-display text-3xl font-extrabold text-primary">{formatPrice(experiencia.price_pen) ?? 'Precio a consultar'}</p>
        {price > 0 && <p className="text-sm text-ink-soft">por persona</p>}
      </div>
      {done ? (
        <>
          <Alert tone="success">Tu reserva quedó registrada como pendiente. Puedes seguirla desde tu cuenta.</Alert>
          <ButtonLink to="/cuenta/reservas" variant="secondary">Ver mis reservas</ButtonLink>
        </>
      ) : user ? (
        <form onSubmit={onSubmit} className="flex flex-col gap-4">
          <TextField label="Fecha" type="date" min={tomorrow} value={date} onChange={(e) => setDate(e.target.value)} required />
          <TextField
            label="Personas"
            type="number"
            min={1}
            max={experiencia.max_participants ?? undefined}
            value={participants}
            onChange={(e) => setParticipants(Math.max(1, Number(e.target.value)))}
            required
          />
          <TextField label="Teléfono de contacto" type="tel" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="+51 9…" />
          <TextArea label="Notas para el guía" value={notes} onChange={(e) => setNotes(e.target.value)} maxLength={500} className="min-h-20" />
          {price > 0 && (
            <p className="flex justify-between border-t border-line pt-4 font-semibold">
              <span>Total estimado</span> <span>{formatPrice(price * participants)}</span>
            </p>
          )}
          {error && <Alert tone="error">{error}</Alert>}
          <Button type="submit" loading={book.isPending}>Reservar</Button>
          <p className="text-xs text-ink-soft">No se cobra nada ahora. El socio confirma la disponibilidad contigo.</p>
        </form>
      ) : (
        <LoginPrompt action="reservar" />
      )}
    </AsideCard>
  )
}

/* ---------- Evento ---------- */

export function EventoDetail() {
  const { data, isLoading, error, refetch } = useDetail<Evento>('eventos')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const e = data
  const days = daysUntil(e.start_time)
  return (
    <DetailShell
      item={e}
      type="eventos"
      kicker={
        <>
          <Badge className="bg-lake text-white">{EVENT_CATEGORIES[e.category] ?? e.category}</Badge>
          {days >= 0 && <Badge className="bg-mint text-ink">{relativeDays(e.start_time)}</Badge>}
        </>
      }
      meta={
        <>
          <p className="flex items-center gap-2">
            <CalendarDays className="size-4.5" aria-hidden /> {formatDate(e.start_time)}, {formatTime(e.start_time)} – {formatDate(e.end_time) === formatDate(e.start_time) ? formatTime(e.end_time) : `${formatDate(e.end_time)}, ${formatTime(e.end_time)}`}
          </p>
          <LocaleLink locale={e.locale} />
        </>
      }
      aside={<AttendanceCard evento={e} />}
    >
      {e.content && <div className="prose-measure mt-6 whitespace-pre-line text-ink-soft">{e.content}</div>}
    </DetailShell>
  )
}

function AttendanceCard({ evento }: { evento: Evento }) {
  const { user } = useAuth()
  const qc = useQueryClient()
  const notify = useToast()
  const [guests, setGuests] = useState(1)

  const count = useQuery({
    queryKey: ['attendance-count', evento.id],
    queryFn: async () => (await api.get<{ data: { confirmed: number; maybe: number } }>(`/event-attendances/count/${evento.id}`)).data.data,
  })
  const mine = useQuery({
    queryKey: ['attendance', evento.id],
    enabled: !!user,
    queryFn: async () => (await api.get<{ attending: boolean; data: EventAttendance | null }>(`/event-attendances/check/${evento.id}`)).data,
  })

  const refresh = () => {
    void qc.invalidateQueries({ queryKey: ['attendance', evento.id] })
    void qc.invalidateQueries({ queryKey: ['attendance-count', evento.id] })
  }

  const attend = useMutation({
    mutationFn: (status: 'confirmed' | 'maybe') => api.post('/event-attendances', { evento_id: evento.id, status, guests }),
    onSuccess: () => {
      refresh()
      notify('Asistencia registrada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const cancel = useMutation({
    mutationFn: () => api.delete(`/event-attendances/${evento.id}`),
    onSuccess: () => {
      refresh()
      notify('Asistencia cancelada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  const price = formatPrice(evento.ticket_price)
  const attending = mine.data?.attending ? mine.data.data : null

  return (
    <AsideCard>
      <div>
        <p className="font-display text-3xl font-extrabold text-lake">{price ?? 'Entrada libre'}</p>
        {count.data && (
          <p className="mt-1 text-sm text-ink-soft">
            {count.data.confirmed} confirmados{count.data.maybe ? ` · ${count.data.maybe} interesados` : ''}
          </p>
        )}
      </div>
      {!user ? (
        <LoginPrompt action="confirmar asistencia" />
      ) : attending ? (
        <>
          <Alert tone="success">
            {attending.status === 'confirmed' ? 'Confirmaste tu asistencia' : 'Marcaste que quizás vayas'}
            {attending.guests && attending.guests > 1 ? ` con ${attending.guests} personas` : ''}.
          </Alert>
          {attending.status === 'maybe' && (
            <Button onClick={() => attend.mutate('confirmed')} loading={attend.isPending}>Confirmar que voy</Button>
          )}
          <Button variant="secondary" onClick={() => cancel.mutate()} loading={cancel.isPending}>Cancelar asistencia</Button>
        </>
      ) : (
        <>
          <TextField label="¿Cuántos van?" type="number" min={1} max={10} value={guests} onChange={(e) => setGuests(Math.min(10, Math.max(1, Number(e.target.value))))} />
          <Button onClick={() => attend.mutate('confirmed')} loading={attend.isPending && attend.variables === 'confirmed'}>Voy a ir</Button>
          <Button variant="secondary" onClick={() => attend.mutate('maybe')} loading={attend.isPending && attend.variables === 'maybe'}>Quizás vaya</Button>
        </>
      )}
      <a
        href={googleCalendarUrl(evento.title, evento.start_time, evento.end_time, evento.description, evento.formatted_address || evento.address)}
        target="_blank"
        rel="noreferrer"
        className="inline-flex items-center gap-2 font-semibold text-primary"
      >
        <CalendarPlus className="size-4.5" /> Añadir a Google Calendar
      </a>
    </AsideCard>
  )
}

/* ---------- Promoción ---------- */

export function PromocionDetail() {
  const { data, isLoading, error, refetch } = useDetail<Promocion>('promociones')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const p = data
  const discount =
    p.discount_type === '2x1' ? '2 × 1' : p.discount_type === 'percentage' ? `${p.discount_percentage ?? 0}% de descuento` : `${formatPrice(p.discount_amount)} de descuento`
  return (
    <DetailShell
      item={p}
      type="promociones"
      showMap={false}
      kicker={<Badge className="bg-sun text-ink">{discount}</Badge>}
      meta={
        <>
          <p className="flex items-center gap-2"><CalendarDays className="size-4.5" aria-hidden /> Válida del {formatDate(p.start_date)} al {formatDate(p.end_date)}</p>
          <LocaleLink locale={p.locale} />
        </>
      }
      aside={<CouponCard promo={p} discount={discount} />}
    >
      {p.terms_conditions && (
        <section className="mt-10">
          <h2 className="mb-3 text-2xl font-extrabold">Términos y condiciones</h2>
          <p className="prose-measure whitespace-pre-line text-ink-soft">{p.terms_conditions}</p>
        </section>
      )}
    </DetailShell>
  )
}

function CouponCard({ promo, discount }: { promo: Promocion; discount: string }) {
  const { user } = useAuth()
  const notify = useToast()
  const [coupon, setCoupon] = useState<UserCoupon | null>(null)
  const [error, setError] = useState<string | null>(null)

  const claim = useMutation({
    mutationFn: async () => (await api.post<{ data: UserCoupon; message: string }>('/user-coupons', { promocion_id: promo.id })).data,
    onSuccess: (res) => {
      setCoupon(res.data)
      notify(res.message)
    },
    onError: (e) => setError(parseApiError(e).message),
  })

  return (
    <AsideCard>
      <p className="font-display text-3xl font-extrabold">{discount}</p>
      {promo.original_price && promo.final_price && (
        <p className="text-ink-soft">
          <s>{formatPrice(promo.original_price)}</s> <strong className="text-lg text-ink">{formatPrice(promo.final_price)}</strong>
        </p>
      )}
      <p className="text-sm text-ink-soft">Quedan {Math.max(0, daysUntil(promo.end_date))} días para usarla.</p>
      {coupon ? (
        <div className="rounded-2xl border-2 border-dashed border-sun bg-sun/10 p-4 text-center">
          <p className="text-sm text-ink-soft">Tu código</p>
          <p className="font-display text-2xl font-extrabold tracking-wider">{coupon.coupon_code}</p>
          <p className="mt-1 text-xs text-ink-soft">Muéstralo al pagar en el local.</p>
        </div>
      ) : user ? (
        <Button onClick={() => claim.mutate()} loading={claim.isPending} icon={<Ticket className="size-4.5" />}>
          Obtener cupón
        </Button>
      ) : (
        <LoginPrompt action="obtener el cupón" />
      )}
      {error && <Alert tone="error">{error}</Alert>}
    </AsideCard>
  )
}

/* ---------- Local ---------- */

export function LocaleDetail() {
  const { data, isLoading, error, refetch } = useDetail<Locale & { experiencias?: Experiencia[]; eventos?: Evento[]; promociones?: Promocion[] }>('locales')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const l = data
  const related: [ContentType, AnyContent[] | undefined][] = [
    ['promociones', l.promociones],
    ['experiencias', l.experiencias],
    ['eventos', l.eventos],
  ]
  return (
    <DetailShell
      item={l}
      type="locales"
      kicker={<Badge className="bg-mint-dark text-white">{LOCALE_CATEGORIES[l.category] ?? l.category}</Badge>}
      aside={
        <AsideCard>
          <h2 className="text-xl font-bold">Contacto</h2>
          <ul className="flex flex-col gap-3">
            {l.phone && <li><a href={`tel:${l.phone}`} className="flex items-center gap-2 hover:text-primary"><Phone className="size-4.5" /> {l.phone}</a></li>}
            {l.email && <li><a href={`mailto:${l.email}`} className="flex items-center gap-2 break-all hover:text-primary"><Mail className="size-4.5 shrink-0" /> {l.email}</a></li>}
            {l.website && <li><a href={l.website} target="_blank" rel="noreferrer" className="flex items-center gap-2 break-all hover:text-primary"><Globe className="size-4.5 shrink-0" /> {l.website.replace(/^https?:\/\//, '')}</a></li>}
            {(l.formatted_address || l.address) && <li className="flex items-start gap-2"><MapPin className="mt-1 size-4.5 shrink-0" /> {l.formatted_address || l.address}</li>}
          </ul>
          {!l.phone && !l.email && !l.website && <p className="text-ink-soft">Este local aún no publicó sus datos de contacto.</p>}
        </AsideCard>
      }
    >
      {related.map(([type, items]) =>
        items?.length ? (
          <section key={type} className="mt-12">
            <h2 className="mb-5 text-2xl font-extrabold">{CONTENT[type].plural} de este local</h2>
            <div className="grid gap-x-6 gap-y-8 sm:grid-cols-2">
              {items.map((it) => <ContentCard key={it.id} item={it} type={type} />)}
            </div>
          </section>
        ) : null,
      )}
    </DetailShell>
  )
}

/* ---------- Publicación de Candelaria ---------- */

export function CandelariaPostDetail() {
  const { data, isLoading, error, refetch } = useDetail<Candelaria>('candelaria')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  return (
    <DetailShell
      item={data}
      type="candelaria"
      showMap={false}
      kicker={<Badge className="bg-primary text-white">{CANDELARIA_CATEGORIES[data.category] ?? data.category}</Badge>}
      meta={data.event_date ? <p className="flex items-center gap-2"><CalendarDays className="size-4.5" aria-hidden /> {formatDate(data.event_date)}</p> : undefined}
    >
      {data.content && <div className="prose-measure mt-6 whitespace-pre-line text-ink-soft">{data.content}</div>}
    </DetailShell>
  )
}
