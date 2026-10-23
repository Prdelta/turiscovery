import { Navigate } from 'react-router'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'
import Home from './Home'

/** Portada (lago 3D), búsqueda, mapa y modelos 3D */
export const exploreFeature = defineFeature({
  name: 'explore',
  site: [
    // La portada se carga de inmediato: es la primera pantalla de casi todos
    { path: '/', element: <Home /> },
    { path: '/buscar', element: lazyPage(() => import('./SearchPage'), 'default') },
    { path: '/search', element: <Navigate to="/buscar" replace /> },
    { path: '/mapa', element: lazyPage(() => import('./MapPage'), 'default') },
    { path: '/puno-3d', element: lazyPage(() => import('./Puno3DPage'), 'default') },
  ],
})
