import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

export const experiencesFeature = defineFeature({
  name: 'experiences',
  site: [{ path: '/experiencias/:id', element: lazyPage(() => import('./ExperienciaDetail'), 'ExperienciaDetail') }],
})
