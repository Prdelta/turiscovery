import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { CalendarDays, CalendarPlus, ChevronLeft, Globe, Mail, MapPin, Phone, Share2, Star, Ticket } from 'lucide-react'
import { lazy, Suspense, useState, type ReactNode } from 'react'
import { Link, useNavigate, useParams, useSearchParams } from 'react-router'
import { useAuth } from '../auth/AuthContext'
import { ContentCard } from '../components/ContentCard'
import { FavoriteButton } from '../components/FavoriteButton'
import { HostCard } from '../components/HostVoice'
import { ReviewsSection } from '../components/ReviewsSection'
import { TypeTag } from '../components/textile/Tags'
import { WovenBand } from '../components/textile/Weave'
import { Button, ButtonLink } from '../components/ui/Button'
import { Alert, Badge, DifficultyBadge, ErrorState, Spinner } from '../components/ui/Feedback'
import { TextField } from '../components/ui/Field'
import { useToast } from '../components/ui/Toast'
import { api, parseApiError } from '../lib/api'
import { CANDELARIA_CATEGORIES, CONTENT, EVENT_CATEGORIES, LOCALE_CATEGORIES, imageOf, titleOf, type ContentType } from '../lib/content'
import { daysUntil, formatDate, formatPrice, formatTime, googleCalendarUrl, relativeDays } from '../lib/format'
import { useContentItem, useDocumentTitle } from '../lib/hooks'
import { placeFor } from '../lib/places'
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

const OVERLAY_BTN =
  'inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky'

/** Galería: volver, compartir y favorito sobre la foto, y contador de fotos */
function Gallery({ item, type, title }: { item: AnyContent; type: ContentType; title: string }) {
  const navigate = useNavigate()
  const images = item.images?.length ? item.images : [imageOf(item, type)]
  const [active, setActive] = useState(0)
  const back = () => (window.history.length > 1 ? navigate(-1) : navigate(CONTENT[type].path))
  return (
    <div className="flex flex-col gap-3">
      <div className="card overflow-hidden">
        <div className="relative bg-line">
          <img src={images[active]} alt="" className="aspect-[4/3] w-full object-cover sm:aspect-[16/10]" />
          <div className="absolute inset-x-3 top-3 flex justify-between gap-2">
            <button onClick={back} aria-label="Volver" className={OVERLAY_BTN}>
              <ChevronLeft className="size-5" />
            </button>
            <div className="flex gap-2">
              <ShareButton title={title} />
              <FavoriteButton type={type} id={item.id} compact />
            </div>
          </div>
          {images.length > 1 && (
            <span className="absolute bottom-3 right-3 rounded-[var(--radius-chip)] bg-ink px-2.5 py-1 text-sm font-bold text-white">
              {active + 1} / {images.length}
            </span>
          )}
        </div>
        <WovenBand motif={CONTENT[type].motif} dye={CONTENT[type].dye} height={16} />
      </div>
      {images.length > 1 && (
        <div className="flex gap-2 overflow-x-auto pb-1">
          {images.map((src, i) => (
            <button
              key={src + i}
              onClick={() => setActive(i)}
              aria-label={`Ver foto ${i + 1}`}
              aria-pressed={i === active}
              className={clsx('size-20 shrink-0 overflow-hidden rounded-[var(--radius-control)] border-2', i === active ? 'border-ink' : 'border-transparent opacity-80')}
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
    <button onClick={share} aria-label="Compartir" className={OVERLAY_BTN}>
      <Share2 className="size-5" />
    </button>
  )
}

function DetailShell({
  item,
  type,
  kicker,
  meta,
  facts,
  aside,
  bottomBar,
  children,
  showMap = true,
}: {
  item: AnyContent
  type: ContentType
  kicker?: ReactNode
  meta?: ReactNode
  /** Datos clave en cuadrícula, antes de la descripción */
  facts?: ReactNode
  aside?: ReactNode
  /** Barra fija inferior en móvil (precio + acción principal) */
  bottomBar?: ReactNode
  children?: ReactNode
  showMap?: boolean
}) {
  const title = titleOf(item as never)
  const withCoords = item as { latitude?: number | null; longitude?: number | null; formatted_address?: string | null; address?: string | null }
  const coords = withCoords.latitude && withCoords.longitude ? { latitude: withCoords.latitude, longitude: withCoords.longitude } : null
  const place = placeFor(withCoords.latitude, withCoords.longitude)
  const rating = (item as { average_rating?: number }).average_rating
  const reviews = (item as { reviews_count?: number }).reviews_count

  return (
    <article className={clsx('container-page py-6 sm:py-10', bottomBar && 'pb-28 lg:pb-10')}>
      <div className="grid gap-10 lg:grid-cols-[1fr_380px]">
        <div className="min-w-0">
          <Gallery item={item} type={type} title={title} />
          <header className="mt-6">
            <div className="mb-3 flex flex-wrap items-center gap-2">
              <TypeTag type={type} detail={place?.name} />
              {kicker}
            </div>
            <h1 className="t-h1">{title}</h1>
            {rating ? (
              <a href="#resenas" className="mt-2 inline-flex items-center gap-1.5 font-bold hover:underline">
                <Star className="size-4.5 fill-sun-dark text-sun-dark" aria-hidden /> {rating.toFixed(1)}
                {reviews ? <span className="font-normal text-ink-soft">({reviews} reseñas)</span> : null}
              </a>
            ) : null}
            {meta && <div className="mt-4 flex flex-col gap-2 text-ink-soft">{meta}</div>}
          </header>
          {facts && <dl className="mt-6 grid grid-cols-2 gap-3">{facts}</dl>}
          <div className="prose-measure mt-8 whitespace-pre-line text-lg leading-relaxed">{item.description}</div>
          {children}
          {showMap && coords?.latitude && coords?.longitude && (
            <section aria-labelledby="ubicacion" className="mt-12">
              <h2 id="ubicacion" className="t-h2 mb-2">Dónde</h2>
              {(withCoords.formatted_address || withCoords.address) && <p className="mb-4 text-ink-soft">{withCoords.formatted_address || withCoords.address}</p>}
              <div className="card overflow-hidden">
                <Suspense fallback={<div className="skeleton h-80" />}>
                  <MapView points={[{ id: item.id, lat: coords.latitude, lng: coords.longitude, title }]} height={300} />
                </Suspense>
              </div>
              <a
                href={`https://www.google.com/maps/dir/?api=1&destination=${coords.latitude},${coords.longitude}`}
                target="_blank"
                rel="noreferrer"
                className="mt-3 inline-block font-bold text-primary-dark underline decoration-2 underline-offset-4"
              >
                Cómo llegar con Google Maps
              </a>
            </section>
          )}
        </div>
        {aside && <aside className={clsx('lg:sticky lg:top-24 lg:self-start', bottomBar && 'hidden lg:block')}>{aside}</aside>}
      </div>
      <ReviewsSection type={type} id={item.id} />
      {bottomBar && (
        <div className="fixed inset-x-0 bottom-0 z-[1150] border-t-2 border-ink bg-white pb-[env(safe-area-inset-bottom)] lg:hidden">
          <div className="container-page flex items-center justify-between gap-4 py-3">{bottomBar}</div>
        </div>
      )}
    </article>
  )
}

/** Dato de la cuadrícula de información */
function Fact({ label, children, wide }: { label: string; children: ReactNode; wide?: boolean }) {
  return (
    <div className={clsx('card flex flex-col gap-1 p-4', wide && 'col-span-2')}>
      <dt className="t-label text-ink-soft">{label}</dt>
      <dd className="font-bold">{children}</dd>
    </div>
  )
}

function AsideCard({ children }: { children: ReactNode }) {
  return <div className="card flex flex-col gap-5 p-6">{children}</div>
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
  const [params] = useSearchParams()
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const e = data
  const fecha = params.get('fecha')
  const bookHref = `/experiencias/${e.id}/reservar${fecha ? `?fecha=${fecha}` : ''}`
  const price = formatPrice(e.price_pen)

  return (
    <DetailShell
      item={e}
      type="experiencias"
      kicker={e.tags?.map((t) => <Badge key={t}>{t}</Badge>)}
      meta={<LocaleLink locale={e.locale} />}
      facts={
        <>
          <Fact label="Duración">{e.duration_hours ? `${e.duration_hours} horas` : 'A consultar'}</Fact>
          <Fact label="Dificultad">
            <DifficultyBadge level={e.difficulty} />
          </Fact>
          {(e.altitude_m || e.effort_note) && (
            <Fact label="Altura y esfuerzo" wide>
              {e.altitude_m ? `Hasta ${e.altitude_m.toLocaleString('es-PE').replace(',', ' ')} m` : ''}
              {e.altitude_m && e.effort_note ? ' · ' : ''}
              {e.effort_note && <span className="font-normal">{e.effort_note}</span>}
            </Fact>
          )}
          {(e.meeting_point || e.meeting_time) && (
            <Fact label="Punto de encuentro" wide>
              {e.meeting_point}
              {e.meeting_time && (
                <span className="font-normal text-ink-soft">
                  {e.meeting_point ? ' · ' : ''}
                  {e.meeting_time.slice(0, 5)} h
                </span>
              )}
            </Fact>
          )}
          {e.max_participants ? <Fact label="Grupo">Hasta {e.max_participants} personas</Fact> : null}
        </>
      }
      aside={
        <AsideCard>
          <div>
            <p className="font-display text-4xl font-extrabold">{price ?? 'Precio a consultar'}</p>
            {price && price !== 'Gratis' && <p className="text-sm text-ink-soft">por persona</p>}
          </div>
          <ButtonLink to={bookHref} size="lg" fullWidth>Reservar</ButtonLink>
          <p className="text-sm text-ink-soft">No se cobra nada ahora. El socio confirma la fecha contigo.</p>
          <Link to="/planificador" className="text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">
            ¿Qué día te conviene según la altura?
          </Link>
        </AsideCard>
      }
      bottomBar={
        <>
          <p>
            <span className="block font-display text-2xl font-extrabold leading-none">{price ?? 'A consultar'}</span>
            {price && price !== 'Gratis' && <span className="text-sm text-ink-soft">por persona</span>}
          </p>
          <ButtonLink to={bookHref} size="lg">Reservar</ButtonLink>
        </>
      }
    >
      {e.anfitrion && (
        <div className="mt-10">
          <HostCard host={e.anfitrion} />
        </div>
      )}
      {e.content && (
        <section className="mt-10">
          <h2 className="t-h2 mb-3">Itinerario y detalles</h2>
          <div className="prose-measure whitespace-pre-line text-ink-soft">{e.content}</div>
        </section>
      )}
    </DetailShell>
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
