import { accountFeature } from '@/features/account/routes'
import { adminFeature } from '@/features/admin/routes'
import { almanacFeature } from '@/features/almanac/routes'
import { authFeature } from '@/features/auth/routes'
import { bookingsFeature } from '@/features/bookings/routes'
import { candelariaFeature } from '@/features/candelaria/routes'
import { catalogFeature } from '@/features/catalog/routes'
import { eventsFeature } from '@/features/events/routes'
import { experiencesFeature } from '@/features/experiences/routes'
import { exploreFeature } from '@/features/explore/routes'
import { hostsFeature } from '@/features/hosts/routes'
import { infoFeature } from '@/features/info/routes'
import { localesFeature } from '@/features/locales/routes'
import { partnerFeature } from '@/features/partner/routes'
import { plannerFeature } from '@/features/planner/routes'
import { promotionsFeature } from '@/features/promotions/routes'
import { tripFeature } from '@/features/trip/routes'
import type { FeatureModule, NavItem, PanelSection } from '@/shared/routing/feature'
import type { Role } from '@/shared/api/types'

/**
 * Registro de features. Para sumar un dominio: crear features/<dominio>/routes.tsx
 * con defineFeature() y agregarlo aquí; el router y los menús lo toman solos.
 */
export const FEATURES: FeatureModule[] = [
  exploreFeature,
  catalogFeature,
  experiencesFeature,
  bookingsFeature,
  eventsFeature,
  promotionsFeature,
  localesFeature,
  candelariaFeature,
  hostsFeature,
  almanacFeature,
  plannerFeature,
  tripFeature,
  accountFeature,
  partnerFeature,
  adminFeature,
  authFeature,
  infoFeature,
]

/** Paneles: dónde viven, cómo se titulan y quién puede entrar */
export const PANELS: Record<PanelSection, { base: string; title: string; roles?: Role[] }> = {
  account: { base: '/cuenta', title: 'Mi cuenta' },
  partner: { base: '/dashboard', title: 'Panel de socio', roles: ['socio', 'admin'] },
  admin: { base: '/admin', title: 'Administración', roles: ['admin'] },
}

/** Menú de un panel, con rutas absolutas y en orden */
export function panelNav(section: PanelSection): (NavItem & { to: string })[] {
  const base = PANELS[section].base
  return FEATURES.flatMap((f) => f.nav?.[section] ?? [])
    .sort((a, b) => a.order - b.order)
    .map((item) => ({ ...item, to: item.path ? `${base}/${item.path}` : base }))
}
