import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

export const localesFeature = defineFeature({
  name: 'locales',
  site: [{ path: '/locales/:id', element: lazyPage(() => import('./LocaleDetail'), 'LocaleDetail') }],
})
