import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ArrowRight, Mountain, Search, X } from 'lucide-react'
import { lazy, Suspense, useMemo, useState, type FormEvent } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router'
import { TodayCards } from '../components/Almanac'
import { HostPortrait } from '../components/HostVoice'
import { SaveForTripButton } from '../components/OfflineTools'
import { CategoryNavBand } from '../components/textile/Tags'
import { StitchRow, Tocapu, WovenBand } from '../components/textile/Weave'
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
      <LiveBanner />
      <section aria-labelledby="home-title" className="container-page py-6 sm:py-10">
        <Intro selectedId={selectedId} onSelect={select} />

        <div className="mt-6 grid gap-4 lg:grid-cols-[1fr_380px]">
          {/* El lago es la interfaz, dentro de una tarjeta: debajo queda la foto mientras carga el 3D */}
          <div className="card relative h-[52svh] min-h-[340px] overflow-hidden bg-lake lg:h-[560px]">
            <img src="/images/lake_titicaca.webp" alt="" className="absolute inset-0 size-full object-cover" />
            {canRender3D && (
              <Suspense fallback={null}>
                <div className="absolute inset-0">
                  <LakeScene places={PLACES} selected={selectedId} onSelect={select} />
                </div>
              </Suspense>
            )}
            {!selected && (
              <p className="pointer-events-none absolute bottom-3 left-3 rounded-[var(--radius-chip)] border-2 border-ink bg-white px-3 py-1.5 text-sm font-bold">
                Toca un lugar del lago
              </p>
            )}
          </div>

          {selected ? (
            <PlacePanel place={selected} items={byPlace.get(selected.id) ?? []} loading={loading} onClose={() => select(null)} />
          ) : (
            <TripInvite />
          )}
        </div>
      </section>

      <section aria-labelledby="hoy-home" className="container-page pb-12">
        <div className="mb-4 flex items-end justify-between gap-4">
          <h2 id="hoy-home" className="t-h2">Hoy en Puno</h2>
          <Link to="/hoy" className="inline-flex items-center gap-1 font-bold text-primary-dark underline decoration-2 underline-offset-4">
            Almanaque <ArrowRight className="size-4" aria-hidden />
          </Link>
        </div>
        <TodayCards />
      </section>

      <CategoryNavBand />
      <HostsStrip />
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
    <div className="flex flex-col gap-4">
      <div className="flex flex-wrap items-end justify-between gap-4">
        <div className="max-w-2xl">
          <h1 id="home-title" className="t-display">Recorre el Titicaca y elige a dónde ir</h1>
          <p className="mt-2 text-ink-soft">Toca un lugar para ver qué hacer ahí y quién te recibe.</p>
        </div>
        <form onSubmit={onSubmit} role="search" className="flex w-full items-center gap-2 rounded-[var(--radius-control)] border-2 border-ink bg-white p-1.5 pl-4 lg:w-[380px]">
          <Search className="size-5 shrink-0 text-ink-soft" aria-hidden />
          <label htmlFor="hero-search" className="sr-only">Buscar en Turiscovery</label>
          <input
            id="hero-search"
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Kayak, tejidos, cena con show…"
            className="min-w-0 flex-1 bg-transparent py-2 outline-none placeholder:text-ink-soft/80"
          />
          <button type="submit" className="h-11 rounded-[10px] bg-primary px-4 font-bold text-white hover:bg-primary-dark">Buscar</button>
        </form>
      </div>
      <nav aria-label="Lugares del lago" className="-mx-4 overflow-x-auto px-4 pb-1">
        <ul className="flex w-max gap-2">
          {PLACES.map((p) => (
            <li key={p.id}>
              <button
                onClick={() => onSelect(selectedId === p.id ? null : p.id)}
                aria-pressed={selectedId === p.id}
                className={clsx(
                  'inline-flex min-h-11 items-center gap-2 whitespace-nowrap rounded-full border-2 border-ink py-1 pl-1.5 pr-4 text-sm font-bold transition-colors',
                  selectedId === p.id ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky',
                )}
              >
                <Tocapu motif={p.motif} dye={p.dye} size={24} />
                {p.name}
              </button>
            </li>
          ))}
        </ul>
      </nav>
    </div>
  )
}

/** Sin lugar elegido: invitación a guardar el viaje y a planificar */
function TripInvite() {
  return (
    <aside className="card flex flex-col gap-4 p-5">
      <Tocapu motif="isla" dye="lago" size={40} />
      <h2 className="t-h3">En el lago casi no hay señal</h2>
      <p className="text-ink-soft">Guarda tus cupones, reservas y la información de las islas antes de subir al bote.</p>
      <SaveForTripButton variant="primary" fullWidth label="Guardar para el viaje" />
      <StitchRow dye="lago" />
      <p className="text-sm text-ink-soft">¿Primera vez a 3 812 m? Arma tu plan según la altura.</p>
      <ButtonLink to="/planificador" variant="secondary" fullWidth icon={<Mountain className="size-4" />}>
        Planificar mi viaje
      </ButtonLink>
    </aside>
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

/** Hoja del lugar elegido: conteo por tipo, lo publicado ahí y guardar para el viaje */
function PlacePanel({ place, items, loading, onClose }: { place: Place; items: PlacedItem[]; loading: boolean; onClose: () => void }) {
  const counts = (['experiencias', 'eventos', 'promociones', 'locales'] as ContentType[])
    .map((t) => [t, items.filter((i) => i.type === t).length] as const)
    .filter(([, n]) => n > 0)
  const searchTerm = place.name.replace('Islas de los ', '').replace('Isla ', '')

  return (
    <section aria-labelledby="lugar-titulo" className="card flex flex-col overflow-hidden lg:max-h-[560px]">
      <WovenBand motif={place.motif} dye={place.dye} height={16} />
      <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto p-5">
        <div className="flex items-start justify-between gap-3">
          <div>
            <p className="t-label text-ink-soft">{place.short}</p>
            <h2 id="lugar-titulo" className="t-h2">{place.name}</h2>
          </div>
          <button
            onClick={onClose}
            aria-label="Volver a la vista del lago"
            className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white hover:bg-sky"
          >
            <X className="size-5" />
          </button>
        </div>
        <p className="text-ink-soft">{place.description}</p>

        {loading ? (
          <div className="skeleton h-24" />
        ) : items.length ? (
          <>
            <p className="flex flex-wrap gap-2">
              {counts.map(([t, n]) => (
                <span key={t} className="inline-flex min-h-8 items-center gap-1.5 rounded-full border-2 border-ink bg-white py-0.5 pl-1 pr-3 text-sm font-bold">
                  <Tocapu motif={CONTENT[t].motif} dye={CONTENT[t].dye} size={18} /> {n} {(n === 1 ? CONTENT[t].singular : CONTENT[t].plural).toLowerCase()}
                </span>
              ))}
            </p>
            <ul className="flex flex-col divide-y-2 divide-line border-y-2 border-line">
              {items.slice(0, 6).map(({ type, item }) => (
                <li key={`${type}-${item.id}`}>
                  <Link to={`${CONTENT[type].path}/${item.id}`} className="flex min-h-14 items-center gap-3 py-2 hover:bg-sky">
                    <Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={28} />
                    <span className="min-w-0 flex-1">
                      <span className="block truncate font-bold">{titleOf(item as never)}</span>
                      <span className="block text-sm text-ink-soft">
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
          <p className="rounded-[var(--radius-control)] bg-sky p-3 text-sm text-ink-soft">
            Todavía ningún socio publica algo con ubicación aquí. Busca experiencias que salen desde Puno.
          </p>
        )}
      </div>
      {/* Acciones fijas al pie de la hoja: no se van con el desplazamiento de la lista */}
      <div className="flex flex-col gap-2 border-t-2 border-ink p-4">
        <SaveForTripButton variant="primary" fullWidth label="Guardar para el viaje" />
        <ButtonLink to={`/buscar?q=${encodeURIComponent(searchTerm)}`} variant="secondary" fullWidth>
          Todo en {searchTerm}
        </ButtonLink>
      </div>
    </section>
  )
}

function HostsStrip() {
  const { data = [] } = useQuery({ queryKey: ['anfitriones', ''], queryFn: () => getData<Anfitrion[]>('/anfitriones') })
  if (!data.length) return null
  return (
    <section aria-labelledby="anfitriones-home" className="container-page py-14">
      <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div className="flex items-center gap-3">
          <Tocapu motif="persona" dye="qolle" size={40} />
          <div>
            <h2 id="anfitriones-home" className="t-h2">Quién te recibe</h2>
            <p className="text-ink-soft">Escucha su saludo antes de conocerlos.</p>
          </div>
        </div>
        <Link to="/anfitriones" className="inline-flex items-center gap-1 font-bold text-primary-dark underline decoration-2 underline-offset-4">
          Conocer a todos <ArrowRight className="size-4" aria-hidden />
        </Link>
      </div>
      {/* Carrusel con desplazamiento nativo: funciona con el dedo, la rueda y el teclado */}
      <ul tabIndex={0} aria-label="Anfitriones" className="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2">
        {data.slice(0, 10).map((h) => (
          <li key={h.id} className="w-[min(70vw,240px)] shrink-0 snap-start">
            <HostPortrait host={h} />
          </li>
        ))}
      </ul>
    </section>
  )
}

function PlannerInvite() {
  return (
    <section className="container-page pb-16">
      <div className="grid items-stretch overflow-hidden rounded-[var(--radius-panel)] border-2 border-ink bg-lake-deep text-white lg:grid-cols-[1.3fr_1fr]">
        <div className="p-8 sm:p-12">
          <h2 className="t-h1 text-white">Puno está a 3 812 m. Planifica según la altura.</h2>
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
