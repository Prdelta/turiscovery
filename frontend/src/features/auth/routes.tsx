import { Navigate } from 'react-router'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

const pages = () => import('./AuthPages')

export const authFeature = defineFeature({
  name: 'auth',
  site: [
    { path: '/login', element: lazyPage(pages, 'LoginPage') },
    { path: '/registro', element: lazyPage(pages, 'RegisterPage') },
    { path: '/register', element: <Navigate to="/registro" replace /> },
  ],
})
