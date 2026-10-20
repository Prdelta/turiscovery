import { clsx } from 'clsx'
import { ChevronLeft, MapPin, Share2, Star } from 'lucide-react'
import { lazy, Suspense, useState, type ReactNode } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { FavoriteButton } from '@/features/catalog/components/FavoriteButton'
import { ReviewsSection } from '@/features/catalog/components/ReviewsSection'
import { TypeTag } from '@/shared/textile/Tags'
import { WovenBand } from '@/shared/textile/Weave'
import { ButtonLink } from '@/shared/ui/Button'
import { ErrorState, Spinner } from '@/shared/ui/Feedback'
import { useToast } from '@/shared/ui/Toast'
import { parseApiError } from '@/shared/api/client'
import { CONTENT, imageOf, titleOf, type ContentType } from '@/shared/config/content'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { useContentItem } from '@/features/catalog/api'
import { placeFor } from '@/shared/config/places'
import type { AnyContent, Locale } from '@/shared/api/types'

const MapView = lazy(() => import('@/shared/map/Map').then((m) => ({ default: m.MapView })))

export function useDetail<T extends AnyContent>(type: ContentType) {
  const { id } = useParams()
  const query = useContentItem<T>(type, id)
  useDocumentTitle(query.data ? titleOf(query.data as never) : CONTENT[type].singular)
  return query
}

export function DetailState({ isLoading, error, refetch }: { isLoading: boolean; error: unknown; refetch: () => void }) {
  if (isLoading) return <Spinner />
  const status = (error as { response?: { status?: number } })?.response?.status
  return (
    <div className="container-page py-20">
      {status === 404 ? (
        <div className="text-center">
          <h1 className="t-h2">No encontramos esta publicación</h1>
          <p className="mt-2 text-ink-soft">Puede que el socio la haya retirado.</p>
          <Link to="/" className="mt-6 inline-block font-bold text-primary-dark underline decoration-2 underline-offset-4">Volver al inicio</Link>
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

export function DetailShell({
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
export function Fact({ label, children, wide }: { label: string; children: ReactNode; wide?: boolean }) {
  return (
    <div className={clsx('card flex flex-col gap-1 p-4', wide && 'col-span-2')}>
      <dt className="t-label text-ink-soft">{label}</dt>
      <dd className="font-bold">{children}</dd>
    </div>
  )
}

export function AsideCard({ children }: { children: ReactNode }) {
  return <div className="card flex flex-col gap-5 p-6">{children}</div>
}

export function LoginPrompt({ action }: { action: string }) {
  return (
    <ButtonLink to={`/login?next=${encodeURIComponent(location.pathname)}`} className="w-full">
      Inicia sesión para {action}
    </ButtonLink>
  )
}

export function LocaleLink({ locale }: { locale?: Locale | null }) {
  if (!locale) return null
  return (
    <p className="flex items-center gap-2">
      <MapPin className="size-4.5" aria-hidden /> Ofrecido por{' '}
      <Link to={`/locales/${locale.id}`} className="font-bold text-primary-dark underline decoration-2 underline-offset-4">{locale.name}</Link>
    </p>
  )
}
