import axios, { AxiosError } from 'axios'

/**
 * Cliente HTTP para la API de Laravel.
 * La autenticación usa la cookie de sesión de Sanctum (httpOnly) y el token
 * XSRF que Laravel entrega en /sanctum/csrf-cookie.
 */
export const api = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL ?? ''}/api`,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

let csrfReady: Promise<unknown> | null = null

/** Pide la cookie CSRF una sola vez antes de la primera petición que modifica datos */
export function ensureCsrf(force = false) {
  if (!csrfReady || force) {
    csrfReady = axios.get(`${import.meta.env.VITE_API_URL ?? ''}/sanctum/csrf-cookie`, {
      withCredentials: true,
    })
  }
  return csrfReady
}

api.interceptors.request.use(async (config) => {
  const method = (config.method ?? 'get').toLowerCase()
  if (method !== 'get' && method !== 'head') {
    await ensureCsrf()
  }
  return config
})

api.interceptors.response.use(undefined, async (error: AxiosError) => {
  // 419 = token CSRF vencido: se renueva y se reintenta una vez
  const config = error.config as (typeof error.config & { _retried?: boolean }) | undefined
  if (error.response?.status === 419 && config && !config._retried) {
    config._retried = true
    await ensureCsrf(true)
    return api.request(config)
  }
  return Promise.reject(error)
})

export interface ApiEnvelope<T> {
  success: boolean
  message?: string
  data: T
}

export interface Paginated<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

/** GET que devuelve directamente el campo `data` de la respuesta */
export async function getData<T>(url: string, params?: Record<string, unknown>) {
  const res = await api.get<ApiEnvelope<T>>(url, { params: cleanParams(params) })
  return res.data.data
}

function cleanParams(params?: Record<string, unknown>) {
  if (!params) return undefined
  return Object.fromEntries(
    Object.entries(params).filter(([, v]) => v !== undefined && v !== null && v !== ''),
  )
}

/** Mensaje legible y errores por campo a partir de un error de la API */
export function parseApiError(error: unknown): { message: string; fields: Record<string, string> } {
  const fallback = 'No se pudo completar la acción. Revisa tu conexión e inténtalo otra vez.'
  if (!axios.isAxiosError(error)) return { message: fallback, fields: {} }

  const status = error.response?.status
  const body = error.response?.data as
    | { message?: string; errors?: Record<string, string[] | string> }
    | undefined

  const fields: Record<string, string> = {}
  for (const [key, value] of Object.entries(body?.errors ?? {})) {
    fields[key] = Array.isArray(value) ? value[0] : value
  }

  let message = body?.message ?? fallback
  if (status === 401) message = 'Tu sesión terminó. Inicia sesión otra vez.'
  else if (status === 403 && !body?.message) message = 'No tienes permiso para hacer esto.'
  else if (status === 429) message = body?.message ?? 'Demasiados intentos. Espera un momento.'
  else if (status === 422 && Object.keys(fields).length) message = Object.values(fields)[0]
  else if (status && status >= 500) message = 'El servidor tuvo un problema. Inténtalo en unos minutos.'

  return { message, fields }
}
