import { Heart, Home, Settings, Star } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

/** Mi cuenta: resumen, favoritos, reseñas y perfil (reservas, eventos y cupones los aporta su dominio) */
export const accountFeature = defineFeature({
  name: 'account',
  panels: {
    account: [
      { index: true, element: lazyPage(() => import('./AccountHome'), 'AccountHome') },
      { path: 'favoritos', element: lazyPage(() => import('./FavoritesPage'), 'FavoritesPage') },
      { path: 'resenas', element: lazyPage(() => import('./MyReviewsPage'), 'MyReviewsPage') },
      { path: 'perfil', element: lazyPage(() => import('./ProfilePage'), 'ProfilePage') },
    ],
  },
  nav: {
    account: [
      { path: '', label: 'Resumen', order: 0, icon: Home },
      { path: 'favoritos', label: 'Favoritos', order: 10, icon: Heart },
      { path: 'resenas', label: 'Reseñas', order: 50, icon: Star },
      { path: 'perfil', label: 'Perfil', order: 60, icon: Settings },
    ],
  },
})
