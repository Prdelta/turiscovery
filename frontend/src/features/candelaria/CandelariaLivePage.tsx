import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Bell, BellOff, MapPin, Radio } from 'lucide-react'
import { lazy, Suspense, useEffect, useMemo, useRef, useState } from 'react'
import { Link } from 'react-router'
import { Button } from '@/shared/ui/Button'
import { Alert, Badge, ErrorState, Spinner } from '@/shared/ui/Feedback'
import { formatTime } from '@/shared/lib/format'
import { useDocumentTitle, useNow } from '@/shared/lib/hooks'
import { PROGRAM_TYPES } from '@/shared/config/content'
import type { LiveState, Performance, ProgramItem } from '@/shared/api/types'
import { candelariaQueries, useDanzas } from '@/features/candelaria/api'

const MapView = lazy(() => import('@/shared/map/Map').then((m) => ({ default: m.MapView })))

const dayFmt = new Intl.DateTimeFormat('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })

/* ---------- Danzas seguidas (en este dispositivo, sin necesidad de cuenta) ---------- */

const FOLLOW_KEY = 'turiscovery-danzas-seguidas'
const NOTIFIED_KEY = 'turiscovery-avisos-enviados'

function readIds(key: string, storage: Storage): number[] {
  try {
    return JSON.parse(storage.getItem(key) ?? '[]') as number[]
  } catch {
    return []
  }
}

function writeIds(key: string, storage: Storage, ids: number[]) {
  try {
    storage.setItem(key, JSON.stringify(ids))
  } catch {
    // Sin almacenamiento disponible: los avisos funcionan solo mientras la página esté abierta
  }
}

function useFollowedDanzas() {
  const [ids, setIds] = useState<number[]>(() => readIds(FOLLOW_KEY, localStorage))
  const toggle = (id: number) =>
    setIds((prev) => {
      const next = prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
      writeIds(FOLLOW_KEY, localStorage, next)
      return next
    })
  return { ids, toggle }
}

/** Minutos que faltan para una hora estimada */
function minutesUntil(iso?: string) {
  if (!iso) return null
  return Math.max(0, Math.round((new Date(iso).getTime() - Date.now()) / 60_000))
}

export default function CandelariaLivePage() {
  useDocumentTitle('Candelaria en vivo')
  const live = useQuery({ ...candelariaQueries.live(), refetchInterval: (q) => (q.state.data?.live ? 20_000 : 120_000) })
  const program = useQuery({ ...candelariaQueries.program(), staleTime: 300_000 })
  const { ids: followed, toggle } = useFollowedDanzas()
  const state = live.data

  const banner = useAlerts(state, followed)

  return (
    <>
      <section className="bg-lake-deep text-white">
        <div className="container-page py-12 sm:py-16">
          <Link to="/candelaria" className="text-sm font-semibold text-mint hover:underline">Festividad de la Candelaria</Link>
          <h1 className="mt-3 flex flex-wrap items-center gap-4 font-display text-[clamp(2.4rem,6vw,4.4rem)] font-extrabold leading-none text-white">
            Candelaria en vivo
            {state?.live && (
              <span className="inline-flex items-center gap-2 rounded-full bg-danger px-3 py-1 font-sans text-base font-bold">
                <Radio className="size-4 animate-pulse" aria-hidden /> En vivo
              </span>
            )}
          </h1>
          <p className="prose-measure mt-4 text-lg text-white/85">
            Qué conjunto está pasando ahora, cuáles vienen y a qué hora aproximada llegará tu danza favorita. Se actualiza solo cada 20 segundos.
          </p>
        </div>
      </section>

      <div className="container-page py-12">
        {banner && (
          <div className="card sticky top-20 z-[900] mb-6">
            <Alert tone="info">
              <span className="flex items-center gap-2 font-semibold"><Bell className="size-4 shrink-0" aria-hidden /> {banner}</span>
            </Alert>
          </div>
        )}
        {live.isLoading ? (
          <Spinner label="Consultando el desfile…" />
        ) : live.isError && !state ? (
          <ErrorState message="No pudimos consultar el desfile. Revisa tu conexión; lo intentaremos de nuevo en unos segundos." onRetry={() => live.refetch()} />
        ) : state?.live && state.item ? (
          <LiveBoard state={state} followed={followed} />
        ) : (
          <NotLive state={state} />
        )}

        <FollowPanel followed={followed} toggle={toggle} />

        <section aria-labelledby="programa" className="mt-16">
          <h2 id="programa" className="mb-6 t-h2">Programa {program.data?.year ?? ''}</h2>
          {program.isLoading ? (
            <Spinner />
          ) : program.data?.items.length ? (
            <ol className="flex flex-col gap-3">
              {program.data.items.map((item) => <ProgramRow key={item.id} item={item} liveId={state?.live ? state.item?.id : undefined} />)}
            </ol>
          ) : (
            <p className="text-ink-soft">Los organizadores aún no publican el programa de esta edición.</p>
          )}
        </section>
      </div>
    </>
  )
}

function LiveBoard({ state, followed }: { state: LiveState; followed: number[] }) {
  const item = state.item!
  const current = state.current
  const upcoming = state.upcoming ?? []
  const progress = state.total ? Math.round(((state.done_count ?? 0) / state.total) * 100) : 0
  const points = item.latitude && item.longitude ? [{ id: item.id, lat: item.latitude, lng: item.longitude, title: item.location_name ?? item.title }] : []

  return (
    <div className="grid gap-8 lg:grid-cols-[1.2fr_1fr]">
      <div className="flex flex-col gap-6">
        <div>
          <p className="text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">{PROGRAM_TYPES[item.type]} · {item.location_name ?? 'Puno'}</p>
          <h2 className="t-h2">{item.title}</h2>
        </div>

        <div className="rounded-[var(--radius-card)] bg-primary p-6 text-white" aria-live="polite">
          <p className="text-sm font-semibold text-mint">Pasando ahora</p>
          {current ? (
            <>
              <p className="mt-2 font-display t-h1 text-white">{current.group_name}</p>
              {current.danza && <p className="mt-1 text-lg text-white/85">{current.danza.name} · {current.danza.type === 'autoctona' ? 'autóctona' : 'mestiza'}</p>}
              <p className="mt-3 text-sm text-white/75">Conjunto N.º {current.order_number}{current.started_at ? ` · desde las ${formatTime(current.started_at)}` : ''}</p>
            </>
          ) : (
            <p className="mt-2 text-xl text-white">El desfile aún no empieza o está en pausa.</p>
          )}
        </div>

        <div aria-label={`${state.done_count} de ${state.total} conjuntos ya pasaron`}>
          <div className="h-3 overflow-hidden rounded-full bg-line">
            <div className="h-full rounded-full bg-mint-dark" style={{ width: `${progress}%` }} />
          </div>
          <p className="mt-2 text-sm text-ink-soft">
            Pasaron {state.done_count} de {state.total} conjuntos · unos {state.minutes_per_group} min por conjunto
          </p>
        </div>

        {(item.route?.length ?? 0) > 1 || points.length ? (
          <Suspense fallback={<div className="skeleton h-80" />}>
            <MapView points={points} route={item.route} height={340} />
          </Suspense>
        ) : null}
      </div>

      <section aria-labelledby="proximos-conjuntos">
        <h3 id="proximos-conjuntos" className="mb-4 t-h3">Próximos conjuntos</h3>
        {upcoming.length ? (
          <ol className="flex flex-col gap-2">
            {upcoming.map((p) => <UpcomingRow key={p.id} p={p} followed={!!p.candelaria_danza_id && followed.includes(p.candelaria_danza_id)} />)}
          </ol>
        ) : (
          <p className="text-ink-soft">No quedan conjuntos por pasar en esta jornada.</p>
        )}
        <p className="mt-4 text-xs text-ink-soft">Las horas son estimadas con el ritmo real del desfile y pueden cambiar.</p>
      </section>
    </div>
  )
}

function UpcomingRow({ p, followed }: { p: Performance; followed: boolean }) {
  const mins = minutesUntil(p.eta)
  return (
    <li className={clsx('flex items-center gap-3 rounded-[var(--radius-card)] border-2 p-3', followed ? 'border-sun-dark bg-sun/15' : 'border-ink bg-white')}>
      <span className="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-chip)] border-2 border-ink bg-lana font-display font-extrabold">{p.order_number}</span>
      <div className="min-w-0 flex-1">
        <p className="truncate font-semibold">{p.group_name}</p>
        {p.danza && <p className="truncate text-sm text-ink-soft">{p.danza.name}</p>}
      </div>
      <div className="text-right text-sm">
        <p className="font-semibold">{p.eta ? formatTime(p.eta) : ''}</p>
        <p className="text-ink-soft">{mins === 0 ? 'ya llega' : mins !== null ? `en ~${mins} min` : ''}</p>
      </div>
    </li>
  )
}

function NotLive({ state }: { state?: LiveState }) {
  const next = state?.next_item
  return (
    <div className="card p-8">
      <h2 className="t-h2">Ahora no hay ninguna jornada en curso</h2>
      {next ? (
        <p className="mt-2 text-lg text-ink-soft first-letter:uppercase">
          La próxima es <strong className="text-ink">{next.title}</strong>, el {dayFmt.format(new Date(next.starts_at))} a las {formatTime(next.starts_at)}
          {next.location_name ? ` en ${next.location_name}` : ''}.
        </p>
      ) : (
        <p className="mt-2 text-ink-soft">Cuando empiece una jornada del programa verás aquí el desfile en tiempo real.</p>
      )}
      <p className="mt-4 text-ink-soft">Mientras tanto, elige abajo las danzas que no te quieres perder y te avisaremos cuando estén por pasar.</p>
    </div>
  )
}

function ProgramRow({ item, liveId }: { item: ProgramItem; liveId?: number }) {
  const isLive = liveId === item.id
  const now = useNow()
  const past = new Date(item.ends_at) < now
  return (
    <li className={clsx('flex flex-wrap items-center gap-4 card p-4', past && !isLive && 'opacity-60')}>
      <div className="w-36 shrink-0">
        <p className="font-semibold first-letter:uppercase">{dayFmt.format(new Date(item.starts_at))}</p>
        <p className="text-sm text-ink-soft">{formatTime(item.starts_at)} – {formatTime(item.ends_at)}</p>
      </div>
      <div className="min-w-0 flex-1">
        <p className="font-bold">{item.title}</p>
        <p className="flex flex-wrap items-center gap-x-3 text-sm text-ink-soft">
          <span>{PROGRAM_TYPES[item.type]}</span>
          {item.location_name && <span className="inline-flex items-center gap-1"><MapPin className="size-3.5" /> {item.location_name}</span>}
          {item.performances_count ? <span>{item.performances_count} conjuntos</span> : null}
        </p>
      </div>
      {isLive ? <Badge className="border-2 border-cochinilla bg-cochinilla text-white">En vivo</Badge> : past ? <Badge>Terminó</Badge> : null}
    </li>
  )
}

/* ---------- Seguir danzas y avisos ---------- */

function FollowPanel({ followed, toggle }: { followed: number[]; toggle: (id: number) => void }) {
  const { data: danzas = [] } = useDanzas()
  const [permission, setPermission] = useState(() => (typeof Notification === 'undefined' ? 'unsupported' : Notification.permission))

  return (
    <section aria-labelledby="mis-danzas" className="mt-16 card p-6">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h2 id="mis-danzas" className="t-h2">Tus danzas</h2>
          <p className="mt-1 max-w-xl text-ink-soft">Marca las que quieres ver. Cuando una esté a tres conjuntos o menos, te avisamos en esta página.</p>
        </div>
        {permission === 'default' && (
          <Button variant="secondary" icon={<Bell className="size-4" />} onClick={async () => setPermission(await Notification.requestPermission())}>
            Activar avisos del navegador
          </Button>
        )}
        {permission === 'granted' && <Badge className="border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]"><Bell className="size-3.5" /> Avisos activados</Badge>}
        {permission === 'denied' && <Badge><BellOff className="size-3.5" /> Avisos bloqueados en el navegador</Badge>}
      </div>
      <div className="mt-5 flex flex-wrap gap-2">
        {danzas.map((d) => {
          const on = followed.includes(d.id)
          return (
            <button
              key={d.id}
              onClick={() => toggle(d.id)}
              aria-pressed={on}
              className={clsx('inline-flex min-h-11 items-center gap-1.5 whitespace-nowrap rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors', on ? 'bg-sun text-ink' : 'bg-white text-ink hover:bg-sky')}
            >
              {d.name}
            </button>
          )
        })}
        {!danzas.length && <p className="text-sm text-ink-soft">Las danzas aparecerán aquí cuando estén registradas.</p>}
      </div>
      <p className="mt-4 text-xs text-ink-soft">Tus danzas se guardan en este dispositivo. Los avisos funcionan mientras esta página esté abierta.</p>
    </section>
  )
}

/** Aviso (en la página y, si se permite, del navegador) cuando una danza seguida está cerca */
function useAlerts(state: LiveState | undefined, followed: number[]) {
  const [banner, setBanner] = useState<string | null>(null)
  const notified = useRef<number[]>(readIds(NOTIFIED_KEY, sessionStorage))

  const soon = useMemo(
    () => (state?.upcoming ?? []).filter((p) => (p.position ?? 99) <= 3 && p.candelaria_danza_id && followed.includes(p.candelaria_danza_id)),
    [state, followed],
  )

  useEffect(() => {
    for (const p of soon) {
      if (notified.current.includes(p.id)) continue
      notified.current = [...notified.current, p.id]
      writeIds(NOTIFIED_KEY, sessionStorage, notified.current)
      const text = `${p.danza?.name ?? 'Tu danza'}: ${p.group_name} está por pasar${p.eta ? ` (~${formatTime(p.eta)})` : ''}.`
      setBanner(text)
      if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
        new Notification('Candelaria en vivo', { body: text, icon: '/pwa-192x192.png', tag: `danza-${p.id}` })
      }
    }
  }, [soon])

  useEffect(() => {
    if (!banner) return
    const t = setTimeout(() => setBanner(null), 15_000)
    return () => clearTimeout(t)
  }, [banner])

  // En una pestaña de fondo, el título también avisa
  useEffect(() => {
    document.title = banner ? `🔔 ${banner}` : 'Candelaria en vivo · Turiscovery'
  }, [banner])

  return banner
}

export function LiveBanner() {
  const { data } = useQuery(candelariaQueries.live())
  if (!data?.live || !data.item) return null
  return (
    <Link to="/candelaria/en-vivo" className="block bg-danger text-white">
      <span className="container-page flex items-center gap-3 py-3 font-semibold">
        <Radio className="size-4 animate-pulse" aria-hidden />
        En vivo: {data.item.title}
        {data.current ? ` · pasa ${data.current.group_name}` : ''}
        <span className="ml-auto underline underline-offset-4">Seguir el desfile</span>
      </span>
    </Link>
  )
}
