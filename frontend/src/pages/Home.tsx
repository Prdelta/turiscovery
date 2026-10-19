import { ArrowDown, Box, Mountain, Search } from 'lucide-react'
import { lazy, Suspense, useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router'
import { ContentCard } from '../components/ContentCard'
import { ButtonLink } from '../components/ui/Button'
import { CardGridSkeleton } from '../components/ui/Feedback'
import { useContentList, useDocumentTitle } from '../lib/hooks'
import type { Evento, Experiencia, Promocion } from '../lib/types'
import { webglAvailable } from '../three/support'

const LakeScene = lazy(() => import('../three/LakeScene'))

const PILLARS = [
  { to: '/candelaria', title: 'Candelaria', text: 'Danzas, historia y galería de la fiesta de febrero.', img: '/images/puno_dancers.webp' },
  { to: '/experiencias', title: 'Experiencias', text: 'Tours al lago, caminatas y talleres con guías locales.', img: '/images/exp_titicaca.webp' },
  { to: '/eventos', title: 'Eventos', text: 'Conciertos, ferias y fiestas con fecha y lugar.', img: '/images/evt_candelaria.webp' },
  { to: '/promociones', title: 'Promociones', text: 'Descuentos vigentes en restaurantes y hoteles.', img: '/images/promo_food.webp' },
]

export default function Home() {
  useDocumentTitle()
  return (
    <>
      <Hero />
      <UpcomingEvents />
      <Pillars />
      <FeaturedExperiences />
      <CandelariaBand />
      <ActivePromotions />
      <Explore3D />
    </>
  )
}

function Hero() {
  const navigate = useNavigate()
  const [q, setQ] = useState('')
  const [canRender3D] = useState(() => webglAvailable())

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    navigate(`/buscar${q.trim() ? `?q=${encodeURIComponent(q.trim())}` : ''}`)
  }

  return (
    <section className="relative h-[100svh] min-h-[560px] overflow-hidden bg-lake text-white">
      <div className="absolute inset-0" aria-hidden>
        {/* La foto queda debajo mientras la escena 3D carga (el canvas es transparente hasta su primer cuadro) */}
        <img src="/images/lake_titicaca.webp" alt="" className="absolute inset-0 size-full object-cover" />
        {canRender3D && (
          <Suspense fallback={null}>
            <div className="absolute inset-0">
              <LakeScene />
            </div>
          </Suspense>
        )}
      </div>

      <div className="container-page relative flex h-full flex-col justify-end pb-14 sm:pb-20">
        <p className="mb-4 text-base font-semibold text-mint [text-shadow:0_1px_12px_rgb(10_27_64/0.7)]">Puno · 3 812 m s. n. m.</p>
        <h1 className="max-w-4xl font-display [text-shadow:0_2px_24px_rgb(10_27_64/0.55)] text-[clamp(2.6rem,7vw,5.6rem)] font-extrabold leading-[0.98] text-white">
          El lago más alto del mundo, contado por quienes viven en su orilla
        </h1>
        <form onSubmit={onSubmit} role="search" className="mt-8 flex w-full max-w-xl items-center gap-2 rounded-full bg-white p-1.5 pl-5 text-ink">
          <Search className="size-5 shrink-0 text-ink-soft" aria-hidden />
          <label htmlFor="hero-search" className="sr-only">Buscar en Turiscovery</label>
          <input
            id="hero-search"
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Uros, Taquile, Candelaria, cena con show…"
            className="min-w-0 flex-1 bg-transparent py-2 outline-none placeholder:text-ink-soft/70"
          />
          <button type="submit" className="h-11 rounded-full bg-primary px-5 font-semibold text-white hover:bg-primary-dark">
            Buscar
          </button>
        </form>
        <div className="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/85">
          <Link to="/planificador" className="inline-flex items-center gap-2 font-semibold hover:text-mint">
            <Mountain className="size-4" aria-hidden /> Planifica tu viaje según la altura
          </Link>
          <Link to="/puno-3d" className="inline-flex items-center gap-2 font-semibold hover:text-mint">
            <Box className="size-4" aria-hidden /> Recorre el lago en 3D
          </Link>
          <a href="#esta-semana" className="inline-flex items-center gap-2 hover:text-mint">
            <ArrowDown className="size-4" aria-hidden /> Qué hacer esta semana
          </a>
        </div>
      </div>
    </section>
  )
}

function SectionHead({ id, title, text, link }: { id?: string; title: string; text?: string; link?: { to: string; label: string } }) {
  return (
    <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h2 id={id} className="text-3xl font-extrabold sm:text-4xl">{title}</h2>
        {text && <p className="mt-2 max-w-xl text-ink-soft">{text}</p>}
      </div>
      {link && (
        <Link to={link.to} className="font-semibold text-primary underline-offset-4 hover:underline">
          {link.label}
        </Link>
      )}
    </div>
  )
}

function UpcomingEvents() {
  const { data, isLoading } = useContentList<Evento>('eventos', { per_page: 6, status: 'upcoming' })
  const items = data?.data ?? []
  return (
    <section aria-labelledby="proximos" className="container-page scroll-mt-16 py-20" id="esta-semana">
      <SectionHead id="proximos" title="Próximos eventos" text="Lo que se viene en la ciudad y alrededor del lago." link={{ to: '/eventos', label: 'Ver todos los eventos' }} />
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : items.length ? (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
          {items.slice(0, 3).map((e) => <ContentCard key={e.id} item={e} type="eventos" />)}
        </div>
      ) : (
        <p className="text-ink-soft">No hay eventos programados por ahora. Vuelve pronto o revisa las experiencias disponibles.</p>
      )}
    </section>
  )
}

function Pillars() {
  return (
    <section aria-labelledby="pilares" className="bg-white py-20">
      <div className="container-page">
        <SectionHead id="pilares" title="Por dónde empezar" />
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {PILLARS.map((p) => (
            <Link key={p.to} to={p.to} className="group relative flex aspect-[3/4] flex-col justify-end overflow-hidden rounded-[var(--radius-card)] bg-lake p-5 text-white">
              <img src={p.img} alt="" loading="lazy" className="absolute inset-0 size-full object-cover opacity-80 transition-transform duration-500 group-hover:scale-105" />
              <div className="absolute inset-x-0 bottom-0 h-1/2 bg-lake-deep/70" aria-hidden />
              <div className="relative">
                <h3 className="font-display text-3xl font-extrabold text-white">{p.title}</h3>
                <p className="mt-1 text-sm text-white/85">{p.text}</p>
              </div>
            </Link>
          ))}
        </div>
        <p className="mt-6 text-ink-soft">
          ¿Buscas dónde comer o dormir?{' '}
          <Link to="/locales" className="font-semibold text-primary underline-offset-4 hover:underline">Revisa los locales asociados</Link>{' '}
          o ubícalos en el <Link to="/mapa" className="font-semibold text-primary underline-offset-4 hover:underline">mapa</Link>.
        </p>
      </div>
    </section>
  )
}

function FeaturedExperiences() {
  const { data, isLoading } = useContentList<Experiencia>('experiencias', { per_page: 6 })
  const items = data?.data ?? []
  if (!isLoading && !items.length) return null
  return (
    <section aria-labelledby="experiencias-home" className="container-page py-20">
      <SectionHead id="experiencias-home" title="Experiencias" text="Reservas directas con agencias y guías de Puno." link={{ to: '/experiencias', label: 'Ver todas las experiencias' }} />
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
          {items.slice(0, 6).map((e) => <ContentCard key={e.id} item={e} type="experiencias" />)}
        </div>
      )}
    </section>
  )
}

function CandelariaBand() {
  return (
    <section className="bg-lake-deep text-white">
      <div className="container-page grid items-center gap-10 py-20 lg:grid-cols-2">
        <img src="/images/puno_dancers.webp" alt="Danzantes de la Festividad de la Virgen de la Candelaria" loading="lazy" className="aspect-[4/3] w-full rounded-[var(--radius-card)] object-cover" />
        <div>
          <h2 className="text-4xl font-extrabold text-white sm:text-5xl">Festividad de la Virgen de la Candelaria</h2>
          <p className="prose-measure mt-5 text-lg text-white/85">
            Cada febrero, decenas de miles de danzantes y músicos llenan las calles de Puno. La UNESCO la reconoció como Patrimonio
            Cultural Inmaterial de la Humanidad en 2014. Conoce sus danzas autóctonas y mestizas antes de vivirla.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <ButtonLink to="/candelaria" variant="mint">Conocer las danzas</ButtonLink>
            <ButtonLink to="/candelaria#galeria" variant="onDark">Ver la galería</ButtonLink>
          </div>
        </div>
      </div>
    </section>
  )
}

function ActivePromotions() {
  const { data, isLoading } = useContentList<Promocion>('promociones', { per_page: 3 })
  const items = data?.data ?? []
  if (!isLoading && !items.length) return null
  return (
    <section aria-labelledby="promos-home" className="container-page py-20">
      <SectionHead id="promos-home" title="Promociones vigentes" text="Guarda el cupón en tu cuenta y muéstralo en el local." link={{ to: '/promociones', label: 'Ver todas las promociones' }} />
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
          {items.map((p) => <ContentCard key={p.id} item={p} type="promociones" />)}
        </div>
      )}
    </section>
  )
}

function Explore3D() {
  return (
    <section className="container-page pb-24">
      <div className="grid items-center gap-8 rounded-[28px] bg-primary p-8 text-white sm:p-12 lg:grid-cols-[1.3fr_1fr]">
        <div>
          <h2 className="text-3xl font-extrabold text-white sm:text-4xl">Mira el Titicaca antes de llegar</h2>
          <p className="mt-3 max-w-xl text-white/85">
            Recorre en 3D las islas flotantes de los Uros, la isla Taquile y la cordillera, y abre modelos de piezas y lugares de Puno.
          </p>
        </div>
        <div className="flex lg:justify-end">
          <ButtonLink to="/puno-3d" variant="mint" size="lg" icon={<Box className="size-5" />}>
            Abrir Puno en 3D
          </ButtonLink>
        </div>
      </div>
    </section>
  )
}
