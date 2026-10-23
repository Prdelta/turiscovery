import type { Evento, Experiencia } from '@/shared/api/types'

/*
  Reglas de aclimatación para Puno (3 812 m s. n. m.).
  Quien llega desde la costa necesita uno o dos días de actividad suave;
  quien ya viene de otra ciudad alta (Cusco, Arequipa, La Paz) se adapta antes.
  Son pautas generales de viaje, no indicaciones médicas.
*/

export type Origin = 'costa' | 'altura'
export type DayLevel = 'descanso' | 'suave' | 'libre'
type Difficulty = Experiencia['difficulty']

export const ORIGINS: Record<Origin, string> = {
  costa: 'Desde la costa o una ciudad baja (Lima, Trujillo, extranjero)',
  altura: 'Desde otra ciudad alta donde ya pasé unos días (Cusco, La Paz, Arequipa)',
}

export const LEVELS: Record<DayLevel, { label: string; allowed: Difficulty[]; maxHours: number; summary: string; tips: string[] }> = {
  descanso: {
    label: 'Día de aclimatación',
    allowed: ['easy'],
    maxHours: 3,
    summary: 'Tu cuerpo se está adaptando a la altura. Camina despacio y elige actividades cortas y fáciles.',
    tips: [
      'Toma bastante agua y come ligero.',
      'Evita el alcohol y el esfuerzo físico.',
      'Prueba un mate de coca o de muña, como hacen en Puno.',
      'Si tienes dolor de cabeza fuerte o te falta el aire en reposo, descansa y busca atención médica.',
    ],
  },
  suave: {
    label: 'Ritmo suave',
    allowed: ['easy', 'medium'],
    maxHours: 5,
    summary: 'Ya puedes hacer paseos en bote y visitas de medio día, sin caminatas largas en subida.',
    tips: ['Sube las escaleras con calma.', 'Lleva bloqueador y abrigo: el sol quema y en el lago hace frío.'],
  },
  libre: {
    label: 'Cuerpo adaptado',
    allowed: ['easy', 'medium', 'hard'],
    maxHours: 9,
    summary: 'Buen momento para las islas lejanas y las caminatas, como la subida en Taquile.',
    tips: ['Sal temprano: el lago suele estar más calmo por la mañana.'],
  },
}

export function levelForDay(dayIndex: number, origin: Origin): DayLevel {
  if (origin === 'altura') return dayIndex === 0 ? 'suave' : 'libre'
  if (dayIndex === 0) return 'descanso'
  if (dayIndex === 1) return 'suave'
  return 'libre'
}

export function experienceHours(e: Pick<Experiencia, 'duration_hours'>) {
  return e.duration_hours && e.duration_hours > 0 ? e.duration_hours : 3
}

export function fitsLevel(difficulty: Difficulty, level: DayLevel) {
  return LEVELS[level].allowed.includes(difficulty)
}

/* ---------- Plan ---------- */

export interface PlanInput {
  arrival: string // YYYY-MM-DD
  days: number
  origin: Origin
  /** experiencia id -> índice de día (o -1 = sin asignar) */
  experiences: Record<number, number>
  events: number[]
}

export interface PlanDay {
  index: number
  date: Date
  level: DayLevel
  experiences: Experiencia[]
  events: Evento[]
  hours: number
  warnings: string[]
}

export function dayDate(arrival: string, index: number) {
  const [y, m, d] = arrival.split('-').map(Number)
  return new Date(y, m - 1, d + index)
}

export function sameDay(a: Date, b: Date) {
  return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate()
}

/** Primer día de la estadía en que una experiencia encaja según la altura y las horas libres */
export function suggestDay(exp: Experiencia, plan: PlanInput, catalog: Experiencia[]): number {
  const hoursByDay = Array.from({ length: plan.days }, (_, i) =>
    Object.entries(plan.experiences)
      .filter(([, day]) => day === i)
      .reduce((sum, [id]) => sum + experienceHours(catalog.find((c) => c.id === Number(id)) ?? { duration_hours: 3 }), 0),
  )
  for (let i = 0; i < plan.days; i++) {
    const level = levelForDay(i, plan.origin)
    if (fitsLevel(exp.difficulty, level) && hoursByDay[i] + experienceHours(exp) <= LEVELS[level].maxHours) return i
  }
  // Si no entra completa en ningún día: el día compatible con la altura menos sobrecargado
  // (o el último, si ninguno es compatible). El plan mostrará el aviso de horas.
  let best = plan.days - 1
  let bestLoad = Infinity
  for (let i = 0; i < plan.days; i++) {
    const level = levelForDay(i, plan.origin)
    if (!fitsLevel(exp.difficulty, level)) continue
    const load = (hoursByDay[i] + experienceHours(exp)) / LEVELS[level].maxHours
    if (load < bestLoad) {
      bestLoad = load
      best = i
    }
  }
  return best
}

export function buildPlan(plan: PlanInput, experiences: Experiencia[], events: Evento[]): PlanDay[] {
  return Array.from({ length: plan.days }, (_, index) => {
    const date = dayDate(plan.arrival, index)
    const level = levelForDay(index, plan.origin)
    const dayExperiences = experiences.filter((e) => plan.experiences[e.id] === index)
    const dayEvents = events.filter((e) => plan.events.includes(e.id) && sameDay(new Date(e.start_time), date))
    const hours = dayExperiences.reduce((s, e) => s + experienceHours(e), 0)
    const warnings: string[] = []
    for (const e of dayExperiences) {
      if (!fitsLevel(e.difficulty, level)) {
        warnings.push(`“${e.title}” es exigente para el ${index === 0 ? 'día de llegada' : `día ${index + 1}`}. Muévela a un día posterior si puedes.`)
      }
    }
    if (hours > LEVELS[level].maxHours) {
      warnings.push(`Son ${hours} h de actividad; para este día recomendamos hasta ${LEVELS[level].maxHours} h.`)
    }
    if (level === 'descanso' && dayEvents.some((e) => new Date(e.start_time).getHours() >= 20)) {
      warnings.push('Hay un evento nocturno el día que llegas: abrígate bien y vuelve temprano.')
    }
    return { index, date, level, experiences: dayExperiences, events: dayEvents, hours, warnings }
  })
}

/* ---------- Guardado y enlace compartible ---------- */

const STORAGE_KEY = 'turiscovery-plan'

export function savePlan(plan: PlanInput) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(plan))
  } catch {
    // Almacenamiento no disponible (modo privado): el plan sigue en la URL
  }
}

export function loadPlan(): PlanInput | null {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    return raw ? (JSON.parse(raw) as PlanInput) : null
  } catch {
    return null
  }
}

export function planToParams(plan: PlanInput) {
  const p = new URLSearchParams({ llegada: plan.arrival, dias: String(plan.days), desde: plan.origin })
  const exp = Object.entries(plan.experiences).filter(([, d]) => d >= 0).map(([id, d]) => `${id}.${d}`)
  if (exp.length) p.set('exp', exp.join(','))
  if (plan.events.length) p.set('evt', plan.events.join(','))
  return p
}

export function planFromParams(p: URLSearchParams): PlanInput | null {
  const arrival = p.get('llegada')
  if (!arrival || !/^\d{4}-\d{2}-\d{2}$/.test(arrival)) return null
  const days = Math.min(10, Math.max(1, Number(p.get('dias')) || 3))
  const origin: Origin = p.get('desde') === 'altura' ? 'altura' : 'costa'
  const experiences: Record<number, number> = {}
  for (const pair of (p.get('exp') ?? '').split(',').filter(Boolean)) {
    const [id, d] = pair.split('.').map(Number)
    if (id > 0 && d >= 0 && d < days) experiences[id] = d
  }
  const events = (p.get('evt') ?? '').split(',').map(Number).filter((n) => n > 0)
  return { arrival, days, origin, experiences, events }
}

/**
 * Aviso de altura para una reserva: compara la fecha elegida con la llegada del plan guardado.
 * Devuelve null si no hay plan o si la actividad encaja con ese día.
 */
export function altitudeWarningFor(difficulty: Difficulty, bookingDate: string): string | null {
  const plan = loadPlan()
  if (!plan) return null
  const arrival = dayDate(plan.arrival, 0)
  const [y, m, d] = bookingDate.split('-').map(Number)
  const booking = new Date(y, m - 1, d)
  const index = Math.round((booking.getTime() - arrival.getTime()) / 86_400_000)
  if (index < 0 || index >= plan.days) return null
  const level = levelForDay(index, plan.origin)
  if (fitsLevel(difficulty, level)) return null
  return index === 0
    ? 'Esa fecha es el día que llegas a Puno según tu plan. Esta actividad es exigente para tu primer día en la altura: considera reservarla más adelante.'
    : `Esa fecha es tu día ${index + 1} en Puno. Esta actividad podría ser exigente todavía: si puedes, elige un día posterior.`
}
