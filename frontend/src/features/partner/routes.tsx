import { BarChart3 } from 'lucide-react'
import { CONTENT } from '@/shared/config/content'
import { defineFeature, type NavItem } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'
import { MANAGED } from './managed'

const form = () => import('./ContentFormPage')
/** Orden en el menú de cada tipo de contenido que publica el socio */
const ORDER = { experiencias: 20, eventos: 30, promociones: 40, locales: 50, candelaria: 70 } as const

/** Panel de socio: resumen y gestión del contenido propio (los tipos salen del registro CONTENT) */
export const partnerFeature = defineFeature({
  name: 'partner',
  panels: {
    partner: [
      { index: true, element: lazyPage(() => import('./PartnerHome'), 'PartnerHome') },
      { path: ':type', element: lazyPage(() => import('./ContentManager'), 'ContentManager') },
      { path: ':type/nuevo', element: lazyPage(form, 'ContentFormPage') },
      { path: ':type/:id', element: lazyPage(form, 'ContentFormPage') },
    ],
  },
  nav: {
    partner: [
      { path: '', label: 'Resumen', order: 0, icon: BarChart3 },
      ...MANAGED.map(
        (type): NavItem => ({
          path: type,
          label: CONTENT[type].plural,
          order: ORDER[type as keyof typeof ORDER] ?? 80,
          motif: [CONTENT[type].motif, CONTENT[type].dye],
        }),
      ),
    ],
  },
})
