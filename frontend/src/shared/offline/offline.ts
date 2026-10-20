import { useEffect, useState } from 'react'

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
