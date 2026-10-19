import type { Dye, Motif } from '../components/textile/Weave'
import type { AnyContent, ProgramType } from './types'

/** Tipos de contenido que publican los socios */
export type ContentType = 'experiencias' | 'eventos' | 'promociones' | 'locales' | 'candelaria'

export interface ContentMeta {
  type: ContentType
  singular: string
  plural: string
  /** Clase del modelo en Laravel (para reseñas y favoritos) */
  modelClass: string
  /** Ruta pública del detalle */
  path: string
  color: string
  /** Motivo tejido y tinte que identifican al tipo en todo el sitio */
  motif: Motif
  dye: Dye
}

export const CONTENT: Record<ContentType, ContentMeta> = {
  experiencias: {
    type: 'experiencias',
    singular: 'Experiencia',
    plural: 'Experiencias',
    modelClass: 'App\\Models\\Experiencia',
    path: '/experiencias',
    color: 'bg-primary',
    motif: 'olas',
    dye: 'anil',
  },
  eventos: {
    type: 'eventos',
    singular: 'Evento',
    plural: 'Eventos',
    modelClass: 'App\\Models\\Evento',
    path: '/eventos',
    color: 'bg-lake',
    motif: 'chakana',
    dye: 'cochinilla',
  },
  promociones: {
    type: 'promociones',
    singular: 'Promoción',
    plural: 'Promociones',
    modelClass: 'App\\Models\\Promocion',
    path: '/promociones',
    color: 'bg-sun',
    motif: 'ojo',
    dye: 'qolle',
  },
  locales: {
    type: 'locales',
    singular: 'Local',
    plural: 'Locales',
    modelClass: 'App\\Models\\Locale',
    path: '/locales',
    color: 'bg-mint-dark',
    motif: 'llave',
    dye: 'chilca',
  },
  candelaria: {
    type: 'candelaria',
    singular: 'Publicación de Candelaria',
    plural: 'Candelaria',
    modelClass: 'App\\Models\\Candelaria',
    path: '/candelaria/publicaciones',
    color: 'bg-primary-dark',
    motif: 'estrella',
    dye: 'morado',
  },
}

export function typeFromModelClass(modelClass: string): ContentType | undefined {
  return (Object.values(CONTENT).find((m) => m.modelClass === modelClass) ?? undefined)?.type
}

export const LOCALE_CATEGORIES: Record<string, string> = {
  restaurant: 'Restaurante',
  hotel: 'Hotel',
  tour_agency: 'Agencia de turismo',
  craft_shop: 'Artesanía',
  museum: 'Museo',
  cultural_center: 'Centro cultural',
  other: 'Otro',
}

export const EVENT_CATEGORIES: Record<string, string> = {
  concert: 'Concierto',
  festival: 'Festival',
  nightlife: 'Vida nocturna',
  cultural: 'Cultural',
  sports: 'Deportes',
  exhibition: 'Exposición',
  other: 'Otro',
}

export const CANDELARIA_CATEGORIES: Record<string, string> = {
  dance: 'Danza',
  history: 'Historia',
  costume: 'Vestimenta',
  music: 'Música',
  tradition: 'Tradición',
  procession: 'Procesión',
  other: 'Otro',
}

export const DIFFICULTY: Record<string, string> = {
  easy: 'Fácil',
  medium: 'Moderada',
  hard: 'Exigente',
}

export const DISCOUNT_TYPES: Record<string, string> = {
  '2x1': '2 × 1',
  percentage: 'Porcentaje',
  fixed: 'Monto fijo',
}

export function titleOf(item: Partial<AnyContent> & { name?: string; title?: string }) {
  return item.title ?? item.name ?? 'Sin título'
}

/** Imagen de respaldo por tipo cuando el contenido no tiene fotos */
const FALLBACK_IMAGES: Record<ContentType, string> = {
  experiencias: '/images/exp_titicaca.webp',
  eventos: '/images/evt_candelaria.webp',
  promociones: '/images/promo_food.webp',
  locales: '/images/loc_restaurant.webp',
  candelaria: '/images/candelaria_hist.webp',
}

export function imageOf(item: { image_url?: string | null; images?: string[] | null }, type: ContentType) {
  return item.image_url || item.images?.[0] || FALLBACK_IMAGES[type]
}

/** Tipos de jornada del programa de la Candelaria */
export const PROGRAM_TYPES: Record<ProgramType, string> = {
  misa: 'Misa',
  procesion: 'Procesión',
  concurso: 'Concurso',
  entrada: 'Entrada',
  parada: 'Parada',
  otro: 'Actividad',
}
