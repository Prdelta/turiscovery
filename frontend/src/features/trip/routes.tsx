import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

export const tripFeature = defineFeature({
  name: 'trip',
  site: [{ path: '/viaje', element: lazyPage(() => import('./TripPage'), 'default') }],
})
