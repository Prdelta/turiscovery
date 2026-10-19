import { clsx } from 'clsx'
import { Sunrise, Sunset, Thermometer, Wind } from 'lucide-react'
import { Link } from 'react-router'
import {
  ALMANAC_KINDS,
  altiplanoSeason,
  happensOn,
  hourRanges,
  moonInfo,
  moonLitPath,
  useAlmanac,
  useLakeConditions,
  type AlmanacItem,
  type AlmanacKind,
} from '../lib/almanac'
import { formatTime } from '../lib/format'
import { Tocapu, type Dye, type Motif } from './textile/Weave'

const KIND_WEAVE: Record<AlmanacKind, { motif: Motif; dye: Dye }> = {
  fiesta: { motif: 'chakana', dye: 'cochinilla' },
  ritual: { motif: 'sol', dye: 'qolle' },
  agricola: { motif: 'llave', dye: 'chilca' },
  civica: { motif: 'ojo', dye: 'anil' },
  evento: { motif: 'chakana', dye: 'cochinilla' },
  candelaria: { motif: 'estrella', dye: 'morado' },
}

const LAKE_TEXT = {
  calmo: { label: 'Lago calmo', cls: 'bg-mint text-ink' },
  moderado: { label: 'Algo de oleaje', cls: 'bg-sun text-ink' },
  ventoso: { label: 'Lago picado', cls: 'bg-danger text-white' },
}

export function MoonGlyph({ phase, size = 44 }: { phase: number; size?: number }) {
  const { d, transform } = moonLitPath(phase, true)
  return (
    <svg width={size} height={size} viewBox="-1.1 -1.1 2.2 2.2" aria-hidden>
      <circle r="1" fill="#2a3358" />
      <path d={d} transform={transform} fill="#f6efe3" />
    </svg>
  )
}

function itemHref(item: AlmanacItem) {
  if (item.source === 'evento') return `/eventos/${item.id}`
  if (item.source === 'candelaria') return '/candelaria/en-vivo'
  return '/hoy'
}

export function AlmanacRow({ item, dark = false }: { item: AlmanacItem; dark?: boolean }) {
  const weave = KIND_WEAVE[item.kind] ?? KIND_WEAVE.fiesta
  return (
    <li>
      <Link to={itemHref(item)} className={clsx('flex items-start gap-3 rounded-xl p-2 transition-colors', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}>
        <Tocapu motif={weave.motif} dye={weave.dye} size={26} className="mt-0.5" />
        <div className="min-w-0 flex-1">
          <p className="font-semibold leading-snug">{item.title}</p>
          <p className={clsx('text-sm', dark ? 'text-white/70' : 'text-ink-soft')}>
            {item.all_day ? ALMANAC_KINDS[item.kind] : `${formatTime(item.starts_at)} · ${ALMANAC_KINDS[item.kind]}`}
            {item.place_name ? ` · ${item.place_name}` : ''}
          </p>
        </div>
      </Link>
    </li>
  )
}

/**
 * "Hoy en Puno": fecha, luna, estado del lago, época del año y lo que pasa hoy.
 * Versión oscura para ir sobre la escena del lago.
 */
export function TodayPanel({ dark = false, className }: { dark?: boolean; className?: string }) {
  const today = new Date()
  const moon = moonInfo(today)
  const season = altiplanoSeason(today)
  const lake = useLakeConditions()
  const almanac = useAlmanac(7)
  const todayItems = (almanac.data?.items ?? []).filter((i) => happensOn(i, today))
  const dateFmt = new Intl.DateTimeFormat('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })

  return (
    <section
      aria-label="Hoy en Puno"
      className={clsx('flex flex-col gap-4 rounded-[var(--radius-card)] p-5', dark ? 'bg-lake-deep/90 text-white backdrop-blur' : 'bg-white', className)}
    >
      <div className="flex items-start justify-between gap-3">
        <div>
          <p className={clsx('text-sm font-semibold', dark ? 'text-mint' : 'text-primary')}>Hoy en Puno</p>
          <p className={clsx('font-display text-2xl font-extrabold first-letter:uppercase', dark && 'text-white')}>{dateFmt.format(today)}</p>
          <p className={clsx('text-sm', dark ? 'text-white/75' : 'text-ink-soft')}>{season.name} · {moon.name}</p>
        </div>
        <MoonGlyph phase={moon.phase} />
      </div>

      {lake.data ? (
        <div className={clsx('flex flex-col gap-2 rounded-xl p-3', dark ? 'bg-white/10' : 'bg-sky')}>
          <div className="flex flex-wrap items-center gap-2">
            <span className={clsx('rounded-full px-2.5 py-0.5 text-xs font-bold', LAKE_TEXT[lake.data.status].cls)}>{LAKE_TEXT[lake.data.status].label}</span>
            <span className="inline-flex items-center gap-1 text-sm"><Wind className="size-4" aria-hidden /> {lake.data.windNow} km/h</span>
            <span className="inline-flex items-center gap-1 text-sm"><Thermometer className="size-4" aria-hidden /> {lake.data.temperature} °C</span>
          </div>
          <p className={clsx('text-sm', dark ? 'text-white/80' : 'text-ink-soft')}>
            {lake.data.calmHours.length
              ? `Mejores horas para salir en bote: ${hourRanges(lake.data.calmHours)}.`
              : 'Hoy el viento no baja mucho: elige botes grandes o paseos por tierra.'}
          </p>
          <p className={clsx('flex gap-4 text-xs', dark ? 'text-white/70' : 'text-ink-soft')}>
            <span className="inline-flex items-center gap-1"><Sunrise className="size-3.5" aria-hidden /> {formatTime(lake.data.sunrise)}</span>
            <span className="inline-flex items-center gap-1"><Sunset className="size-3.5" aria-hidden /> {formatTime(lake.data.sunset)}</span>
          </p>
        </div>
      ) : lake.isError ? (
        <p className={clsx('text-sm', dark ? 'text-white/70' : 'text-ink-soft')}>El estado del lago no está disponible ahora.</p>
      ) : (
        <div className={clsx('h-20 animate-pulse rounded-xl', dark ? 'bg-white/10' : 'bg-line/70')} />
      )}

      <div>
        <p className={clsx('mb-1 text-sm font-semibold', dark ? 'text-white' : 'text-ink')}>Pasa hoy</p>
        {almanac.isLoading ? (
          <div className={clsx('h-12 animate-pulse rounded-xl', dark ? 'bg-white/10' : 'bg-line/70')} />
        ) : todayItems.length ? (
          <ul className="-mx-2 flex flex-col">
            {todayItems.slice(0, 4).map((i) => <AlmanacRow key={`${i.source}-${i.id}-${i.starts_at}`} item={i} dark={dark} />)}
          </ul>
        ) : (
          <p className={clsx('text-sm', dark ? 'text-white/70' : 'text-ink-soft')}>{season.text}</p>
        )}
        <Link to="/hoy" className={clsx('mt-2 inline-block text-sm font-semibold', dark ? 'text-mint' : 'text-primary')}>
          Ver el almanaque de la semana
        </Link>
      </div>
    </section>
  )
}
