import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Mountain, Search, X } from 'lucide-react'
import { lazy, Suspense, useMemo, useState, type FormEvent } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router'
import { TodayPanel } from '../components/Almanac'
import { HostPortrait } from '../components/HostVoice'
import { SiteBand, Tocapu, WovenBand } from '../components/textile/Weave'
import { ButtonLink } from '../components/ui/Button'
import { getData } from '../lib/api'
import { CONTENT, titleOf, type ContentType } from '../lib/content'
import { formatPrice, formatShortDate } from '../lib/format'
import { useContentList, useDocumentTitle } from '../lib/hooks'
import { PLACES, placeById, placeFor, type Place } from '../lib/places'
import type { Anfitrion, AnyContent, Evento, Experiencia, Locale, Promocion } from '../lib/types'
import { webglAvailable } from '../three/support'
import { LiveBanner } from './CandelariaLivePage'

const LakeScene = lazy(() => import('../three/LakeScene'))

interface PlacedItem {
  type: ContentType
  item: AnyContent
}

/** Agrupa el contenido publicado por lugar del lago, según sus coordenadas */
function usePlacedContent() {
  const experiencias = useContentList<Experiencia>('experiencias', { per_page: 100 })
  const eventos = useContentList<Evento>('eventos', { per_page: 100 })
  const promociones = useContentList<Promocion>('promociones', { per_page: 100 })
  const locales = useContentList<Locale>('locales', { per_page: 100 })

  return useMemo(() => {
    const byPlace = new Map<string, PlacedItem[]>()
    const add = (type: ContentType, items: AnyContent[] | undefined, coords: (i: never) => [unknown, unknown]) => {
      for (const item of items ?? []) {
        const [lat, lng] = coords(item as never)
        const place = placeFor(lat as number, lng as number)
        if (!place) continue
        byPlace.set(place.id, [...(byPlace.get(place.id) ?? []), { type, item }])
      }
    }
    add('experiencias', experiencias.data?.data, (e: Experiencia) => [e.latitude, e.longitude])
    add('eventos', eventos.data?.data, (e: Evento) => [e.latitude, e.longitude])
    add('promociones', promociones.data?.data, (p: Promocion) => [p.locale?.latitude, p.locale?.longitude])
    add('locales', locales.data?.data, (l: Locale) => [l.latitude, l.longitude])
    return { byPlace, loading: experiencias.isLoading || eventos.isLoading }
  }, [experiencias.data, eventos.data, promociones.data, locales.data, experiencias.isLoading, eventos.isLoading])
}

export default function Home() {
  useDocumentTitle()
  const [params, setParams] = useSearchParams()
  const selectedId = params.get('lugar')
  const selected = placeById(selectedId)
  const [canRender3D] = useState(() => webglAvailable())
  const { byPlace, loading } = usePlacedContent()

  const select = (id: string | null) => {
    const next = new URLSearchParams(params)
    if (id) next.set('lugar', id)
    else next.delete('lugar')
    setParams(next, { replace: true, preventScrollReset: true })
  }

  return (
    <>
      <section aria-label="El lago Titicaca" className="relative bg-lake text-white lg:h-[100svh] lg:min-h-[640px]">
        {/* Escena: debajo queda la foto mientras carga el 3D */}
        <div className="relative h-[62svh] min-h-[380px] lg:absolute lg:inset-0 lg:h-auto">
          <img src="/images/lake_titicaca.webp" alt="" className="absolute inset-0 size-full object-cover" />
          {canRender3D && (
            <Suspense fallback={null}>
              <div className="absolute inset-0">
                <LakeScene places={PLACES} selected={selectedId} onSelect={select} />
              </div>
            </Suspense>
          )}
        </div>

        <div className="pointer-events-none relative lg:absolute lg:inset-0">
          <div className="container-page flex h-full flex-col gap-5 py-6 lg:flex-row lg:items-end lg:justify-between lg:pb-8 lg:pt-24">
            <Intro selectedId={selectedId} onSelect={select} />
            <div className="pointer-events-auto w-full lg:w-[380px] lg:self-stretch lg:overflow-y-auto">
              <div className="lg:flex lg:h-full lg:flex-col lg:justify-end">
                {selected ? (
                  <PlacePanel place={selected} items={byPlace.get(selected.id) ?? []} loading={loading} onClose={() => select(null)} />
                ) : (
                  <TodayPanel dark />
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      <LiveBanner />
      <SiteBand />
      <HostsStrip />
      <CategoryLoom />
      <PlannerInvite />
    </>
  )
}

function Intro({ selectedId, onSelect }: { selectedId: string | null; onSelect: (id: string | null) => void }) {
  const navigate = useNavigate()
  const [q, setQ] = useState('')
  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    navigate(`/buscar${q.trim() ? `?q=${encodeURIComponent(q.trim())}` : ''}`)
  }

  return (
    <div className="pointer-events-auto flex max-w-md flex-col gap-3 lg:pb-2">
      <h1 className="font-display text-[clamp(2rem,3.6vw,3rem)] font-extrabold leading-[0.98] text-white [text-shadow:0_2px_24px_rgb(10_27_64/0.6)]">
        Recorre el Titicaca y elige a dónde ir
      </h1>
      <p className="text-sm text-white/90 [text-shadow:0_1px_12px_rgb(10_27_64/0.8)]">Toca un lugar para ver qué hacer ahí y quién te recibe.</p>
      <nav aria-label="Lugares del lago" className="flex flex-wrap gap-2">
        {PLACES.map((p) => (
          <button
            key={p.id}
            onClick={() => onSelect(selectedId === p.id ? null : p.id)}
            aria-pressed={selectedId === p.id}
            className={clsx(
              'inline-flex items-center gap-1.5 rounded-full py-1 pl-1 pr-3 text-sm font-semibold transition-colors',
              selectedId === p.id ? 'bg-mint text-ink' : 'bg-lake-deep/70 text-white backdrop-blur hover:bg-lake-deep',
            )}
          >
            <Tocapu motif={p.motif} dye={p.dye} size={22} />
            {p.name}
          </button>
        ))}
      </nav>
      <form onSubmit={onSubmit} role="search" className="flex w-full items-center gap-2 rounded-full bg-white p-1.5 pl-5 text-ink">
        <Search className="size-5 shrink-0 text-ink-soft" aria-hidden />
        <label htmlFor="hero-search" className="sr-only">Buscar en Turiscovery</label>
        <input
          id="hero-search"
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="Kayak, tejidos, cena con show…"
          className="min-w-0 flex-1 bg-transparent py-2 outline-none placeholder:text-ink-soft/70"
        />
        <button type="submit" className="h-11 rounded-full bg-primary px-5 font-semibold text-white hover:bg-primary-dark">Buscar</button>
      </form>
    </div>
  )
}

function itemLine(type: ContentType, item: AnyContent) {
  switch (type) {
    case 'experiencias':
      return formatPrice((item as Experiencia).price_pen) ?? 'Consultar precio'
    case 'eventos':
      return formatShortDate((item as Evento).start_time)
    case 'promociones':
      return `Hasta el ${formatShortDate((item as Promocion).end_date)}`
    default:
      return ''
  }
}

function PlacePanel({ place, items, loading, onClose }: { place: Place; items: PlacedItem[]; loading: boolean; onClose: () => void }) {
  const counts = (['experiencias', 'eventos', 'promociones', 'locales'] as ContentType[])
    .map((t) => [t, items.filter((i) => i.type === t).length] as const)
    .filter(([, n]) => n > 0)
  const searchTerm = place.name.replace('Islas de los ', '').replace('Isla ', '')

  return (
    <section aria-labelledby="lugar-titulo" className="overflow-hidden rounded-[var(--radius-card)] bg-lake-deep/95 text-white backdrop-blur">
      <WovenBand motif={place.motif} dye={place.dye} height={16} />
      <div className="flex flex-col gap-4 p-5">
        <div className="flex items-start justify-between gap-3">
          <div>
            <p className="text-sm font-semibold text-mint">{place.short}</p>
            <h2 id="lugar-titulo" className="font-display text-3xl font-extrabold text-white">{place.name}</h2>
          </div>
          <button onClick={onClose} aria-label="Volver a la vista del lago" className="rounded-full p-2 hover:bg-white/10">
            <X className="size-5" />
          </button>
        </div>
        <p className="text-white/85">{place.description}</p>

        {loading ? (
          <div className="h-24 animate-pulse rounded-xl bg-white/10" />
        ) : items.length ? (
          <>
            <p className="flex flex-wrap gap-2 text-xs font-semibold">
              {counts.map(([t, n]) => (
                <span key={t} className="inline-flex items-center gap-1.5 rounded-full bg-white/10 py-0.5 pl-0.5 pr-2.5">
                  <Tocapu motif={CONTENT[t].motif} dye={CONTENT[t].dye} size={18} /> {n} {CONTENT[t].plural.toLowerCase()}
                </span>
              ))}
            </p>
            <ul className="-mx-2 flex flex-col">
              {items.slice(0, 6).map(({ type, item }) => (
                <li key={`${type}-${item.id}`}>
                  <Link to={`${CONTENT[type].path}/${item.id}`} className="flex items-center gap-3 rounded-xl p-2 hover:bg-white/10">
                    <Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={26} />
                    <span className="min-w-0 flex-1">
                      <span className="block truncate font-semibold">{titleOf(item as never)}</span>
                      <span className="block text-sm text-white/70">
                        {CONTENT[type].singular}
                        {itemLine(type, item) ? ` · ${itemLine(type, item)}` : ''}
                      </span>
                    </span>
                  </Link>
                </li>
              ))}
            </ul>
          </>
        ) : (
          <p className="rounded-xl bg-white/10 p-3 text-sm text-white/80">
            Todavía ningún socio publica algo con ubicación aquí. Busca experiencias que salen desde Puno.
          </p>
        )}
        <div className="flex flex-wrap gap-2">
          <ButtonLink to={`/buscar?q=${encodeURIComponent(searchTerm)}`} variant="mint" size="sm">
            Buscar “{searchTerm}”
          </ButtonLink>
          <ButtonLink to="/planificador" variant="onDark" size="sm" icon={<Mountain className="size-4" />}>Planificar</ButtonLink>
        </div>
      </div>
    </section>
  )
}

function HostsStrip() {
  const { data = [] } = useQuery({ queryKey: ['anfitriones', ''], queryFn: () => getData<Anfitrion[]>('/anfitriones') })
  if (!data.length) return null
  return (
    <section aria-labelledby="anfitriones-home" className="container-page py-20">
      <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div className="flex items-center gap-4">
          <Tocapu motif="persona" dye="qolle" size={44} />
          <div>
            <h2 id="anfitriones-home" className="text-3xl font-extrabold sm:text-4xl">Quién te recibe</h2>
            <p className="mt-1 text-ink-soft">Escucha su saludo antes de conocerlos.</p>
          </div>
        </div>
        <Link to="/anfitriones" className="font-semibold text-primary underline-offset-4 hover:underline">Conocer a todos</Link>
      </div>
      <div className="grid grid-cols-2 gap-x-5 gap-y-8 lg:grid-cols-4">
        {data.slice(0, 4).map((h) => <HostPortrait key={h.id} host={h} />)}
      </div>
    </section>
  )
}

/** Índice tejido: cada categoría es un paño con su motivo */
function CategoryLoom() {
  const tiles: { type: ContentType; text: string }[] = [
    { type: 'experiencias', text: 'Tours al lago, caminatas y talleres' },
    { type: 'eventos', text: 'Conciertos, ferias y fiestas con fecha' },
    { type: 'promociones', text: 'Descuentos vigentes con cupón' },
    { type: 'locales', text: 'Dónde comer, dormir y comprar' },
    { type: 'candelaria', text: 'Danzas, programa y desfile en vivo' },
  ]
  return (
    <section aria-label="Explorar por tipo" className="bg-white py-16">
      <div className="container-page grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        {tiles.map(({ type, text }) => {
          const meta = CONTENT[type]
          const to = type === 'candelaria' ? '/candelaria' : meta.path
          return (
            <Link key={type} to={to} className="group flex flex-col overflow-hidden rounded-[var(--radius-card)] border border-line bg-sky transition-colors hover:border-ink/30">
              <WovenBand motif={meta.motif} dye={meta.dye} height={28} />
              <div className="flex flex-1 flex-col gap-2 p-5">
                <Tocapu motif={meta.motif} dye={meta.dye} size={40} />
                <h3 className="text-xl font-extrabold group-hover:text-primary">{meta.plural}</h3>
                <p className="text-sm text-ink-soft">{text}</p>
              </div>
            </Link>
          )
        })}
      </div>
    </section>
  )
}

function PlannerInvite() {
  return (
    <section className="container-page py-20">
      <div className="grid items-stretch overflow-hidden rounded-[28px] bg-lake-deep text-white lg:grid-cols-[1.3fr_1fr]">
        <div className="p-8 sm:p-12">
          <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Puno está a 3 812 m. Planifica según la altura.</h2>
          <p className="mt-3 max-w-xl text-white/85">
            Te decimos qué día conviene cada actividad para que llegues a Taquile con el cuerpo listo, no el día que aterrizas.
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <ButtonLink to="/planificador" variant="mint" icon={<Mountain className="size-5" />}>Armar mi plan</ButtonLink>
            <ButtonLink to="/hoy" variant="onDark">Ver el almanaque</ButtonLink>
          </div>
        </div>
        <div className="hidden lg:flex lg:flex-col">
          {(['sol', 'olas', 'persona', 'chakana', 'estrella'] as const).map((m, i) => (
            <WovenBand key={m} motif={m} dye={(['qolle', 'anil', 'cochinilla', 'chilca', 'morado'] as const)[i]} height={64} className="flex-1" />
          ))}
        </div>
      </div>
    </section>
  )
}
