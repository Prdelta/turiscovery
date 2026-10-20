import { useEffect, useState } from 'react'
import { api } from './api'
import { loadPlan } from './altitude'

/** Caché de respuestas de la API que guarda el service worker (ver vite.config.ts) */
const API_CACHE = 'turiscovery-api'

export function useOnline() {
  const [online, setOnline] = useState(() => (typeof navigator === 'undefined' ? true : navigator.onLine))
  useEffect(() => {
    const on = () => setOnline(true)
    const off = () => setOnline(false)
    window.addEventListener('online', on)
    window.addEventListener('offline', off)
    return () => {
      window.removeEventListener('online', on)
      window.removeEventListener('offline', off)
    }
  }, [])
  return online
}

/**
 * Borra las respuestas de la API guardadas en el dispositivo.
 * Se llama al cerrar sesión para que otra persona no vea cupones o reservas sin conexión.
 */
export async function clearPrivateCaches() {
  if (typeof caches === 'undefined') return
  await caches.delete(API_CACHE).catch(() => false)
}

/**
 * Descarga ahora los datos que el viajero necesita en el lago y las islas, donde no hay señal:
 * cupones, reservas, asistencias, favoritos, el programa de la Candelaria y las experiencias del plan.
 * El service worker guarda cada respuesta para usarla después sin conexión.
 */
export async function saveForTrip(loggedIn: boolean): Promise<{ saved: number; failed: number }> {
  const urls = ['/experiencias?per_page=100', '/eventos?per_page=100', '/promociones?per_page=100', '/locales?per_page=100', '/candelaria-danzas', '/candelaria-program', '/candelaria-live']
  if (loggedIn) urls.push('/user-coupons', '/bookings', '/event-attendances', '/favorites', '/profile', '/session/check')

  const plan = loadPlan()
  for (const id of Object.keys(plan?.experiences ?? {})) urls.push(`/experiencias/${id}`)
  for (const id of plan?.events ?? []) urls.push(`/eventos/${id}`)

  const results = await Promise.allSettled(urls.map((u) => api.get(u)))
  const saved = results.filter((r) => r.status === 'fulfilled').length
  if (saved) {
    try {
      localStorage.setItem(SAVED_AT_KEY, new Date().toISOString())
    } catch {
      // Sin almacenamiento: solo no se muestra la fecha del último guardado
    }
  }
  return { saved, failed: results.length - saved }
}

const SAVED_AT_KEY = 'turiscovery-viaje-guardado'

/** Fecha del último "Guardar para usar sin conexión" en este dispositivo */
export function lastSavedAt(): Date | null {
  try {
    const v = localStorage.getItem(SAVED_AT_KEY)
    return v ? new Date(v) : null
  } catch {
    return null
  }
}

/* ---------- Instalar como app ---------- */

interface InstallPromptEvent extends Event {
  prompt: () => Promise<void>
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>
}

let deferred: InstallPromptEvent | null = null
const listeners = new Set<() => void>()

if (typeof window !== 'undefined') {
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferred = e as InstallPromptEvent
    listeners.forEach((l) => l())
  })
  window.addEventListener('appinstalled', () => {
    deferred = null
    listeners.forEach((l) => l())
  })
}

/** Devuelve una función para instalar la app, o null si el navegador no lo ofrece (o ya está instalada) */
export function useInstallPrompt() {
  const [, force] = useState(0)
  useEffect(() => {
    const l = () => force((n) => n + 1)
    listeners.add(l)
    return () => {
      listeners.delete(l)
    }
  }, [])
  if (!deferred) return null
  return async () => {
    const ev = deferred
    if (!ev) return
    await ev.prompt()
    await ev.userChoice
    deferred = null
    listeners.forEach((l) => l())
  }
}
