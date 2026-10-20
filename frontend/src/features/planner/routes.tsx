import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

export const plannerFeature = defineFeature({
  name: 'planner',
  site: [{ path: '/planificador', element: lazyPage(() => import('./PlannerPage'), 'default') }],
})
