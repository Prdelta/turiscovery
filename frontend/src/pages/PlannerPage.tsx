import { clsx } from 'clsx'
import { CalendarDays, Clock, Copy, Mountain, Plus, Printer, Share2, Trash2 } from 'lucide-react'
import { useEffect, useMemo, useState } from 'react'
import { Link, useSearchParams } from 'react-router'
import { SaveForTripButton } from '../components/OfflineTools'
import { Button } from '../components/ui/Button'
import { Alert, Badge, Spinner } from '../components/ui/Feedback'
import { useToast } from '../components/ui/Toast'
import {
  LEVELS,
  ORIGINS,
  buildPlan,
  dayDate,
  experienceHours,
  fitsLevel,
  levelForDay,
  loadPlan,
  planFromParams,
  planToParams,
  savePlan,
  suggestDay,
  type DayLevel,
  type Origin,
  type PlanInput,
} from '../lib/altitude'
import { DIFFICULTY } from '../lib/content'
import { formatTime, toDateInput } from '../lib/format'
import { useContentList, useDocumentTitle } from '../lib/hooks'
import type { Evento, Experiencia } from '../lib/types'

const LEVEL_STYLE: Record<DayLevel, string> = {
  descanso: 'border-2 border-ink bg-sky text-ink',
  suave: 'border-2 border-sun-dark bg-sun/20 text-[#6b4f00]',
  libre: 'border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]',
}

const dayFmt = new Intl.DateTimeFormat('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })

function defaultPlan(): PlanInput {
  return { arrival: toDateInput(new Date(Date.now() + 7 * 86_400_000).toISOString()), days: 3, origin: 'costa', experiences: {}, events: [] }
}

export default function PlannerPage() {
  useDocumentTitle('Planifica tu viaje')
  const [params, setParams] = useSearchParams()
  const notify = useToast()
  const [plan, setPlan] = useState<PlanInput>(() => planFromParams(params) ?? loadPlan() ?? defaultPlan())

  // El plan vive en la URL (para compartir) y en el dispositivo (para volver después)
  useEffect(() => {
    setParams(planToParams(plan), { replace: true })
    savePlan(plan)
  }, [plan, setParams])

  const experiencesQ = useContentList<Experiencia>('experiencias', { per_page: 100 })
  const eventsQ = useContentList<Evento>('eventos', { per_page: 100 })
  const experiences = useMemo(() => experiencesQ.data?.data ?? [], [experiencesQ.data])
  const allEvents = useMemo(() => eventsQ.data?.data ?? [], [eventsQ.data])

  const stayEnd = dayDate(plan.arrival, plan.days)
  const stayEvents = allEvents.filter((e) => {
    const start = new Date(e.start_time)
    return start >= dayDate(plan.arrival, 0) && start < stayEnd
  })
  const days = buildPlan(plan, experiences, stayEvents)

  const update = (patch: Partial<PlanInput>) =>
    setPlan((p) => {
      const next = { ...p, ...patch, experiences: { ...(patch.experiences ?? p.experiences) } }
      // Si se acorta la estadía, las experiencias de días que ya no existen vuelven a sugerirse
      if (patch.days !== undefined) {
        for (const [id, d] of Object.entries(next.experiences)) if (d >= next.days) next.experiences[Number(id)] = next.days - 1
      }
      return next
    })

  const addExperience = (e: Experiencia) => update({ experiences: { ...plan.experiences, [e.id]: suggestDay(e, plan, experiences) } })
  const moveExperience = (id: number, day: number) => update({ experiences: { ...plan.experiences, [id]: day } })
  const removeExperience = (id: number) => {
    const rest = { ...plan.experiences }
    delete rest[id]
    update({ experiences: rest })
  }
  const toggleEvent = (id: number) =>
    update({ events: plan.events.includes(id) ? plan.events.filter((x) => x !== id) : [...plan.events, id] })

  const shareUrl = `${window.location.origin}/planificador?${planToParams(plan)}`
  const shareText = `Mi plan para Puno (${plan.days} días desde el ${dayFmt.format(dayDate(plan.arrival, 0))}): ${shareUrl}`

  return (
    <div className="container-page py-12 sm:py-16">
      <header className="grid gap-6 lg:grid-cols-[1.4fr_1fr] lg:items-end">
        <div>
          <p className="mb-3 inline-flex items-center gap-2 font-bold text-primary-dark underline decoration-2 underline-offset-4">
            <Mountain className="size-5" aria-hidden /> Puno está a 3 812 m de altura
          </p>
          <h1 className="t-h1">Planifica tu viaje según la altura</h1>
          <p className="prose-measure mt-4 text-lg text-ink-soft">
            Elige qué quieres hacer y te sugerimos en qué día hacerlo para que tu cuerpo se adapte: lo suave al llegar, las islas lejanas y
            caminatas cuando ya estés aclimatado.
          </p>
        </div>
        <p className="text-sm text-ink-soft lg:text-right">
          Son recomendaciones generales de viaje. Si tienes una condición cardíaca, respiratoria o estás embarazada, consulta a tu médico antes de viajar.
        </p>
      </header>

      <section aria-label="Datos del viaje" className="mt-10 grid gap-5 card p-6 md:grid-cols-[180px_160px_1fr]">
        <div className="flex flex-col gap-1.5">
          <label htmlFor="llegada" className="text-sm font-semibold">Llego a Puno el</label>
          <input id="llegada" type="date" className="field-input" value={plan.arrival} onChange={(e) => e.target.value && update({ arrival: e.target.value })} />
        </div>
        <div className="flex flex-col gap-1.5">
          <label htmlFor="dias" className="text-sm font-semibold">Me quedo</label>
          <select id="dias" className="field-input" value={plan.days} onChange={(e) => update({ days: Number(e.target.value) })}>
            {[1, 2, 3, 4, 5, 6, 7].map((n) => <option key={n} value={n}>{n} día{n > 1 ? 's' : ''}</option>)}
          </select>
        </div>
        <fieldset className="flex flex-col gap-1.5">
          <legend className="mb-1.5 text-sm font-semibold">Llego</legend>
          <div className="grid gap-2 sm:grid-cols-2">
            {(Object.keys(ORIGINS) as Origin[]).map((o) => (
              <label
                key={o}
                className={clsx(
                  'flex cursor-pointer items-start gap-2 rounded-[var(--radius-control)] border-2 p-3 text-sm',
                  plan.origin === o ? 'border-ink bg-sky' : 'border-line bg-white hover:border-ink',
                )}
              >
                <input type="radio" name="origen" className="mt-1 accent-primary" checked={plan.origin === o} onChange={() => update({ origin: o })} />
                {ORIGINS[o]}
              </label>
            ))}
          </div>
        </fieldset>
      </section>

      <div className="mt-10 grid gap-10 lg:grid-cols-[1fr_400px]">
        {/* ---------- Plan día a día ---------- */}
        <section aria-labelledby="mi-plan">
          <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 id="mi-plan" className="t-h2">Tu plan</h2>
            <div className="flex flex-wrap gap-2 print:hidden">
              <a
                href={`https://wa.me/?text=${encodeURIComponent(shareText)}`}
                target="_blank"
                rel="noreferrer"
                className="inline-flex min-h-11 items-center gap-1.5 rounded-[var(--radius-control)] border-2 border-ink bg-mint px-4 text-sm font-bold text-ink hover:bg-white"
              >
                <Share2 className="size-4" /> WhatsApp
              </a>
              <Button
                size="sm"
                variant="secondary"
                icon={<Copy className="size-4" />}
                onClick={async () => {
                  await navigator.clipboard.writeText(shareUrl)
                  notify('Enlace del plan copiado')
                }}
              >
                Copiar enlace
              </Button>
              <Button size="sm" variant="secondary" icon={<Printer className="size-4" />} onClick={() => window.print()}>
                Imprimir
              </Button>
              <SaveForTripButton />
            </div>
          </div>
          <ol className="flex flex-col gap-4">
            {days.map((day) => {
              const level = LEVELS[day.level]
              return (
                <li key={day.index} className="card p-5">
                  <div className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                      <h3 className="t-h3 first-letter:uppercase">
                        Día {day.index + 1} · {dayFmt.format(day.date)}
                      </h3>
                      <p className="mt-1 max-w-xl text-sm text-ink-soft">{level.summary}</p>
                    </div>
                    <Badge className={LEVEL_STYLE[day.level]}>{level.label}</Badge>
                  </div>

                  <div className="mt-4" aria-label={`${day.hours} de ${level.maxHours} horas recomendadas`}>
                    <div className="h-2 overflow-hidden rounded-full bg-line">
                      <div
                        className={clsx('h-full rounded-full', day.hours > level.maxHours ? 'bg-cochinilla' : 'bg-primary')}
                        style={{ width: `${Math.min(100, (day.hours / level.maxHours) * 100)}%` }}
                      />
                    </div>
                    <p className="mt-1 text-xs text-ink-soft">{day.hours} h de {level.maxHours} h recomendadas</p>
                  </div>

                  {(day.events.length > 0 || day.experiences.length > 0) ? (
                    <ul className="mt-4 flex flex-col divide-y-2 divide-line">
                      {day.events.map((e) => (
                        <li key={`e-${e.id}`} className="flex items-center gap-3 py-2.5">
                          <CalendarDays className="size-4.5 shrink-0 text-lake" aria-hidden />
                          <Link to={`/eventos/${e.id}`} className="flex-1 font-bold hover:text-primary-dark hover:underline">{e.title}</Link>
                          <span className="text-sm text-ink-soft">{formatTime(e.start_time)}</span>
                          <button onClick={() => toggleEvent(e.id)} aria-label={`Quitar ${e.title}`} className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white print:hidden">
                            <Trash2 className="size-4" />
                          </button>
                        </li>
                      ))}
                      {day.experiences.map((e) => (
                        <li key={`x-${e.id}`} className="flex flex-wrap items-center gap-3 py-2.5">
                          <Clock className="size-4.5 shrink-0 text-primary" aria-hidden />
                          <Link to={`/experiencias/${e.id}?fecha=${toDateInput(day.date.toISOString())}`} className="min-w-0 flex-1 font-bold hover:text-primary-dark hover:underline">
                            {e.title}
                          </Link>
                          <span className="text-sm text-ink-soft">{experienceHours(e)} h · {DIFFICULTY[e.difficulty]}</span>
                          <label className="sr-only" htmlFor={`mover-${e.id}`}>Mover a otro día</label>
                          <select
                            id={`mover-${e.id}`}
                            value={day.index}
                            onChange={(ev) => moveExperience(e.id, Number(ev.target.value))}
                            className="min-h-11 rounded-[var(--radius-chip)] border-2 border-ink bg-white px-2 text-sm print:hidden"
                          >
                            {days.map((d) => (
                              <option key={d.index} value={d.index}>
                                Día {d.index + 1}{fitsLevel(e.difficulty, d.level) ? '' : ' (exigente)'}
                              </option>
                            ))}
                          </select>
                          <button onClick={() => removeExperience(e.id)} aria-label={`Quitar ${e.title}`} className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white print:hidden">
                            <Trash2 className="size-4" />
                          </button>
                        </li>
                      ))}
                    </ul>
                  ) : (
                    <p className="mt-4 text-sm text-ink-soft">
                      {day.level === 'descanso' ? 'Día libre para aclimatarte. Un paseo corto por la Plaza de Armas o el puerto es suficiente.' : 'Nada planificado aún.'}
                    </p>
                  )}

                  {day.warnings.length > 0 && (
                    <div className="mt-4 flex flex-col gap-2">
                      {day.warnings.map((w) => <Alert key={w} tone="error">{w}</Alert>)}
                    </div>
                  )}

                  <details className="mt-4 text-sm">
                    <summary className="cursor-pointer font-bold text-primary-dark underline decoration-2 underline-offset-4">Consejos para este día</summary>
                    <ul className="mt-2 list-disc pl-5 text-ink-soft">
                      {level.tips.map((t) => <li key={t}>{t}</li>)}
                    </ul>
                  </details>
                </li>
              )
            })}
          </ol>
        </section>

        {/* ---------- Catálogo ---------- */}
        <aside className="flex flex-col gap-8 print:hidden" aria-label="Qué puedes agregar">
          <section aria-labelledby="cat-eventos">
            <h2 id="cat-eventos" className="mb-3 t-h3">Eventos en tus fechas</h2>
            {eventsQ.isLoading ? (
              <Spinner />
            ) : eventsQ.isError ? (
              <p className="text-sm font-bold text-cochinilla">No se pudieron cargar los eventos. Revisa tu conexión.</p>
            ) : stayEvents.length ? (
              <ul className="flex flex-col gap-2">
                {stayEvents.map((e) => {
                  const added = plan.events.includes(e.id)
                  return (
                    <li key={e.id} className="flex items-center gap-3 card p-3">
                      <div className="min-w-0 flex-1">
                        <p className="truncate font-semibold">{e.title}</p>
                        <p className="text-xs text-ink-soft first-letter:uppercase">{dayFmt.format(new Date(e.start_time))}, {formatTime(e.start_time)}</p>
                      </div>
                      <Button size="sm" variant={added ? 'secondary' : 'primary'} onClick={() => toggleEvent(e.id)}>
                        {added ? 'Quitar' : 'Agregar'}
                      </Button>
                    </li>
                  )
                })}
              </ul>
            ) : (
              <p className="text-sm text-ink-soft">No hay eventos publicados para esas fechas.</p>
            )}
          </section>

          <section aria-labelledby="cat-exp">
            <h2 id="cat-exp" className="mb-1 t-h3">Experiencias</h2>
            <p className="mb-3 text-sm text-ink-soft">Al agregarlas, las ponemos en el primer día en que tu cuerpo estará listo.</p>
            {experiencesQ.isLoading ? (
              <Spinner />
            ) : experiencesQ.isError ? (
              <p className="text-sm font-bold text-cochinilla">No se pudieron cargar las experiencias. Revisa tu conexión.</p>
            ) : experiences.length ? (
              <ul className="flex flex-col gap-2">
                {experiences.map((e) => {
                  const day = plan.experiences[e.id]
                  const added = day !== undefined
                  const earliest = Array.from({ length: plan.days }, (_, i) => i).find((i) => fitsLevel(e.difficulty, levelForDay(i, plan.origin)))
                  return (
                    <li key={e.id} className="flex items-center gap-3 card p-3">
                      <div className="min-w-0 flex-1">
                        <p className="truncate font-semibold">{e.title}</p>
                        <p className="text-xs text-ink-soft">
                          {DIFFICULTY[e.difficulty]} · {experienceHours(e)} h
                          {earliest === undefined ? ' · necesita más días de aclimatación' : earliest > 0 ? ` · desde el día ${earliest + 1}` : ''}
                        </p>
                      </div>
                      {added ? (
                        <span className="flex items-center gap-2">
                          <span className="text-xs font-bold text-primary-dark underline decoration-2 underline-offset-4">Día {day + 1}</span>
                          <Button size="sm" variant="secondary" onClick={() => removeExperience(e.id)}>Quitar</Button>
                        </span>
                      ) : (
                        <Button size="sm" icon={<Plus className="size-4" />} onClick={() => addExperience(e)}>Agregar</Button>
                      )}
                    </li>
                  )
                })}
              </ul>
            ) : (
              <p className="text-sm text-ink-soft">Aún no hay experiencias publicadas.</p>
            )}
          </section>
        </aside>
      </div>
    </div>
  )
}
