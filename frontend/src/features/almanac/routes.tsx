import { Sun } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

/** Almanaque vivo: hoy en Puno, fechas del calendario andino y su administración */
export const almanacFeature = defineFeature({
  name: 'almanac',
  site: [{ path: '/hoy', element: lazyPage(() => import('./AlmanacPage'), 'default') }],
  panels: { admin: [{ path: 'almanaque', element: lazyPage(() => import('./admin/AlmanacAdmin'), 'default') }] },
  nav: { admin: [{ path: 'almanaque', label: 'Almanaque', order: 20, icon: Sun }] },
})
