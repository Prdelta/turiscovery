import { Ticket } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

/** Promociones: ficha con cupón y los cupones del viajero */
export const promotionsFeature = defineFeature({
  name: 'promotions',
  site: [{ path: '/promociones/:id', element: lazyPage(() => import('./PromocionDetail'), 'PromocionDetail') }],
  panels: { account: [{ path: 'cupones', element: lazyPage(() => import('./CouponsPage'), 'CouponsPage') }] },
  nav: { account: [{ path: 'cupones', label: 'Cupones', order: 40, icon: Ticket }] },
})
