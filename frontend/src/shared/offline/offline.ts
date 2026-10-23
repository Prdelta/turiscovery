import { useEffect, useState } from 'react'

/** Caché de respuestas de la API que guarda el service worker (ver vite.config.ts) */
const API_CACHE = 'nanpuno-api'

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

/* ---------- Nombre anterior (Turiscovery) ---------- */

const LEGACY_PREFIX = 'turiscovery-'
const PREFIX = 'nanpuno-'

/**
 * La app se llamaba Turiscovery y guardaba el plan, el viaje y las cachés con ese prefijo.
 * Copia lo guardado a las claves nuevas (sin pisar lo ya migrado) y borra las viejas, para que
 * nadie pierda su plan ni lo guardado para el lago. Se puede borrar cuando ya nadie use la versión anterior.
 */
export function migrateLegacyStorage() {
  for (const storage of [safeStorage('localStorage'), safeStorage('sessionStorage')]) {
    if (!storage) continue
    const legacy = Array.from({ length: storage.length }, (_, i) => storage.key(i)).filter((k): k is string => !!k?.startsWith(LEGACY_PREFIX))
    for (const key of legacy) {
      const renamed = PREFIX + key.slice(LEGACY_PREFIX.length)
      if (storage.getItem(renamed) === null) storage.setItem(renamed, storage.getItem(key) ?? '')
      storage.removeItem(key)
    }
  }
  void migrateLegacyCaches()
}

function safeStorage(name: 'localStorage' | 'sessionStorage'): Storage | null {
  try {
    return window[name]
  } catch {
    // Almacenamiento bloqueado (modo privado o permisos del navegador)
    return null
  }
}

async function migrateLegacyCaches() {
  if (typeof caches === 'undefined') return
  try {
    for (const name of await caches.keys()) {
      if (!name.startsWith(LEGACY_PREFIX)) continue
      const [from, to] = await Promise.all([caches.open(name), caches.open(PREFIX + name.slice(LEGACY_PREFIX.length))])
      for (const request of await from.keys()) {
        const response = await from.match(request)
        if (response && !(await to.match(request))) await to.put(request, response)
      }
      await caches.delete(name)
    }
  } catch {
    // Si falla, la caché vieja queda: el service worker vuelve a llenar la nueva al navegar
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
