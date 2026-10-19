const dateFmt = new Intl.DateTimeFormat('es-PE', { day: 'numeric', month: 'long', year: 'numeric' })
const shortDateFmt = new Intl.DateTimeFormat('es-PE', { day: 'numeric', month: 'short' })
const timeFmt = new Intl.DateTimeFormat('es-PE', { hour: '2-digit', minute: '2-digit' })
const money = new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' })

export function formatDate(value?: string | null) {
  if (!value) return ''
  return dateFmt.format(new Date(value))
}

export function formatShortDate(value?: string | null) {
  if (!value) return ''
  return shortDateFmt.format(new Date(value))
}

export function formatTime(value?: string | null) {
  if (!value) return ''
  return timeFmt.format(new Date(value))
}

export function formatPrice(value?: string | number | null) {
  if (value === null || value === undefined || value === '') return null
  const n = Number(value)
  if (Number.isNaN(n)) return null
  return n === 0 ? 'Gratis' : money.format(n)
}

/** Días que faltan hasta una fecha (0 = hoy, negativo = ya pasó) */
export function daysUntil(value: string) {
  const ms = new Date(value).setHours(0, 0, 0, 0) - new Date().setHours(0, 0, 0, 0)
  return Math.round(ms / 86_400_000)
}

export function relativeDays(value: string) {
  const d = daysUntil(value)
  if (d === 0) return 'Hoy'
  if (d === 1) return 'Mañana'
  if (d > 1) return `En ${d} días`
  if (d === -1) return 'Ayer'
  return `Hace ${Math.abs(d)} días`
}

/** Valor para <input type="datetime-local"> */
export function toDateTimeInput(value?: string | null) {
  if (!value) return ''
  const d = new Date(value)
  const pad = (n: number) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

export function toDateInput(value?: string | null) {
  return toDateTimeInput(value).slice(0, 10)
}

export function initials(name: string) {
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((p) => p[0]?.toUpperCase())
    .join('')
}

/** Enlace para añadir un evento a Google Calendar */
export function googleCalendarUrl(title: string, start: string, end: string, details: string, location?: string | null) {
  const fmt = (v: string) => new Date(v).toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '')
  const params = new URLSearchParams({
    action: 'TEMPLATE',
    text: title,
    dates: `${fmt(start)}/${fmt(end)}`,
    details,
    location: location ?? '',
  })
  return `https://calendar.google.com/calendar/render?${params}`
}
