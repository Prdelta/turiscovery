import { loadPlan } from '@/features/planner/altitude'
import { api } from '@/shared/api/client'

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
