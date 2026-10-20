import { useQuery } from '@tanstack/react-query'
import { getData } from '@/shared/api/client'

/* ---------- Luna ---------- */

const SYNODIC = 29.530588853
// Luna nueva de referencia: 6 de enero de 2000, 18:14 UTC
const KNOWN_NEW_MOON = Date.UTC(2000, 0, 6, 18, 14)

export interface MoonInfo {
  /** 0 = luna nueva, 0.5 = llena */
  phase: number
  illumination: number
  name: string
}

export function moonInfo(date = new Date()): MoonInfo {
  const age = (((date.getTime() - KNOWN_NEW_MOON) / 86_400_000) % SYNODIC + SYNODIC) % SYNODIC
  const phase = age / SYNODIC
  const illumination = (1 - Math.cos(2 * Math.PI * phase)) / 2
  const names: [number, string][] = [
    [0.034, 'Luna nueva'],
    [0.216, 'Luna creciente'],
    [0.284, 'Cuarto creciente'],
    [0.466, 'Gibosa creciente'],
    [0.534, 'Luna llena'],
    [0.716, 'Gibosa menguante'],
    [0.784, 'Cuarto menguante'],
    [0.966, 'Luna menguante'],
    [1, 'Luna nueva'],
  ]
  const name = names.find(([limit]) => phase < limit)?.[1] ?? 'Luna nueva'
  return { phase, illumination, name }
}

/**
 * Silueta iluminada de la luna (radio 1, centrada en 0,0).
 * En el hemisferio sur, como en Puno, la luna creciente se ilumina por la izquierda.
 */
export function moonLitPath(phase: number, southern = true) {
  const r = 1
  const rx = Math.abs(Math.cos(2 * Math.PI * phase)) * r
  const waxing = phase < 0.5
  const crescent = phase < 0.25 || phase > 0.75
  let d: string
  if (waxing) {
    d = `M0 ${-r} A${r} ${r} 0 0 1 0 ${r} A${rx} ${r} 0 0 ${crescent ? 0 : 1} 0 ${-r}Z`
  } else {
    d = `M0 ${-r} A${r} ${r} 0 0 0 0 ${r} A${rx} ${r} 0 0 ${crescent ? 1 : 0} 0 ${-r}Z`
  }
  return { d, transform: southern ? 'scale(-1 1)' : undefined }
}

/* ---------- Época del año en el altiplano ---------- */

export interface Season {
  name: string
  text: string
}

export function altiplanoSeason(date = new Date()): Season {
  const m = date.getMonth() + 1
  if (m === 12 || m <= 3) {
    return { name: 'Época de lluvias', text: 'Las tardes traen lluvia y granizo; los campos están verdes y es tiempo de carnavales y de la Candelaria.' }
  }
  if (m <= 5) {
    return { name: 'Tiempo de cosecha', text: 'Se recoge la papa y la quinua. Los días son templados y las noches empiezan a enfriar.' }
  }
  if (m <= 8) {
    return { name: 'Época de heladas', text: 'Cielos despejados, sol intenso y noches bajo cero: las familias hacen chuño con las heladas.' }
  }
  return { name: 'Tiempo de siembra', text: 'Se prepara la tierra y se siembra esperando las primeras lluvias. Hace más calor al mediodía.' }
}

/* ---------- Estado del lago (Open-Meteo) ---------- */

export interface LakeConditions {
  windNow: number
  temperature: number
  sunrise: string
  sunset: string
  status: 'calmo' | 'moderado' | 'ventoso'
  /** Horas de hoy con luz (6–17 h) y viento suave para navegar */
  calmHours: number[]
}

const PUNO = { latitude: -15.84, longitude: -70.02 }

/** Umbrales orientativos para paseos en bote (km/h a 10 m de altura) */
export function windStatus(kmh: number): LakeConditions['status'] {
  if (kmh < 12) return 'calmo'
  if (kmh < 22) return 'moderado'
  return 'ventoso'
}

async function fetchLake(): Promise<LakeConditions> {
  const params = new URLSearchParams({
    latitude: String(PUNO.latitude),
    longitude: String(PUNO.longitude),
    current: 'temperature_2m,wind_speed_10m',
    hourly: 'wind_speed_10m',
    daily: 'sunrise,sunset',
    timezone: 'America/Lima',
    forecast_days: '1',
  })
  const res = await fetch(`https://api.open-meteo.com/v1/forecast?${params}`)
  if (!res.ok) throw new Error('Sin datos del clima')
  const data = await res.json()
  const hours: string[] = data.hourly.time
  const winds: number[] = data.hourly.wind_speed_10m
  const calmHours = hours
    .map((t, i) => ({ hour: Number(t.slice(11, 13)), wind: winds[i] }))
    .filter((h) => h.hour >= 6 && h.hour <= 17 && h.wind < 12)
    .map((h) => h.hour)
  return {
    windNow: Math.round(data.current.wind_speed_10m),
    temperature: Math.round(data.current.temperature_2m),
    sunrise: data.daily.sunrise[0],
    sunset: data.daily.sunset[0],
    status: windStatus(data.current.wind_speed_10m),
    calmHours,
  }
}

export function useLakeConditions() {
  return useQuery({ queryKey: almanacKeys.lake(), queryFn: fetchLake, staleTime: 15 * 60_000, retry: 1 })
}

/** Resume horas consecutivas: [7,8,9,13,14] → "7–10 h y 13–15 h" */
export function hourRanges(hours: number[]) {
  if (!hours.length) return ''
  const ranges: [number, number][] = []
  for (const h of hours) {
    const last = ranges[ranges.length - 1]
    if (last && h === last[1] + 1) last[1] = h
    else ranges.push([h, h])
  }
  return ranges.map(([a, b]) => `${a}–${b + 1} h`).join(' y ')
}

/* ---------- Almanaque (API) ---------- */

export type AlmanacKind = 'fiesta' | 'ritual' | 'agricola' | 'civica' | 'evento' | 'candelaria'

export interface AlmanacItem {
  source: 'almanaque' | 'evento' | 'candelaria'
  id: number
  title: string
  description?: string | null
  kind: AlmanacKind
  place_name?: string | null
  province?: string | null
  latitude?: number | null
  longitude?: number | null
  starts_at: string
  ends_at: string
  all_day: boolean
}

export const ALMANAC_KINDS: Record<AlmanacKind, string> = {
  fiesta: 'Fiesta',
  ritual: 'Ritual andino',
  agricola: 'Calendario agrícola',
  civica: 'Fecha cívica',
  evento: 'Evento',
  candelaria: 'Candelaria',
}

export function localDateKey(d: Date) {
  const pad = (n: number) => String(n).padStart(2, '0')
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`
}

/** Almanaque: fechas por rango, estado del lago y la lista del admin */
export const almanacKeys = {
  all: ['almanac'] as const,
  lake: () => ['almanac-lake'] as const,
  admin: () => [...almanacKeys.all, 'admin'] as const,
}

export function useAlmanac(days = 7, from?: string) {
  return useQuery({
    queryKey: [...almanacKeys.all, from ?? 'hoy', days],
    queryFn: () => getData<{ from: string; to: string; items: AlmanacItem[] }>('/almanaque', { from, days }),
    staleTime: 10 * 60_000,
  })
}

/** ¿El ítem ocurre en ese día (fecha local)? */
export function happensOn(item: AlmanacItem, day: Date) {
  const start = new Date(item.starts_at)
  const end = new Date(item.ends_at)
  const dayStart = new Date(day.getFullYear(), day.getMonth(), day.getDate())
  const dayEnd = new Date(day.getFullYear(), day.getMonth(), day.getDate(), 23, 59, 59)
  return start <= dayEnd && end >= dayStart
}
