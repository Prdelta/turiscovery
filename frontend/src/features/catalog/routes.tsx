import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

const list = () => import('./ListPage')

/** Listados públicos por tipo de contenido (las fichas viven en cada dominio) */
export const catalogFeature = defineFeature({
  name: 'catalog',
  site: (['experiencias', 'eventos', 'promociones', 'locales'] as const).map((type) => ({
    path: `/${type}`,
    element: lazyPage(list, 'default', { type }),
  })),
})
