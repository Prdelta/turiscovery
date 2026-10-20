import type { LucideIcon } from 'lucide-react'
import type { ComponentType } from 'react'
import type { RouteObject } from 'react-router'
import type { Dye, Motif } from '@/shared/textile/Weave'

/** Paneles con menú propio: Mi cuenta, panel de socio y administración */
export type PanelSection = 'account' | 'partner' | 'admin'

export interface NavItem {
  /** Ruta relativa a la sección; '' es el inicio de la sección */
  path: string
  label: string
  /** Posición en el menú (menor primero); deja huecos de 10 para insertar sin reordenar */
  order: number
  /** Ícono de interfaz (Lucide) o, para tipos de contenido, su motivo tejido */
  icon?: LucideIcon
  motif?: [Motif, Dye]
  /** Contador opcional a la derecha (por ejemplo, reservas pendientes). Devuelve null si no hay nada. */
  Badge?: ComponentType
}

/**
 * Lo que aporta cada feature a la app. El router y los menús de los paneles se arman
 * juntando estos módulos: agregar un dominio nuevo no obliga a tocar el router ni los layouts.
 */
export interface FeatureModule {
  name: string
  /** Páginas del sitio público (dentro de SiteLayout); rutas absolutas */
  site?: RouteObject[]
  /** Páginas dentro de cada panel; rutas relativas a la sección (index para su inicio) */
  panels?: Partial<Record<PanelSection, RouteObject[]>>
  /** Entradas del menú de cada panel */
  nav?: Partial<Record<PanelSection, NavItem[]>>
}

export function defineFeature(module: FeatureModule): FeatureModule {
  return module
}
