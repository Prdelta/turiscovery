import { BarChart3, Search, Users } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

/** Administración: supervisión, usuarios y búsqueda de recursos */
export const adminFeature = defineFeature({
  name: 'admin',
  panels: {
    admin: [
      { index: true, element: lazyPage(() => import('./AdminHome'), 'AdminHome') },
      { path: 'usuarios', element: lazyPage(() => import('./UsersPage'), 'UsersPage') },
      { path: 'usuarios/:id', element: lazyPage(() => import('./UserFormPage'), 'UserFormPage') },
      { path: 'recursos', element: lazyPage(() => import('./ResourceSearchPage'), 'ResourceSearchPage') },
    ],
  },
  nav: {
    admin: [
      { path: '', label: 'Supervisión', order: 0, icon: BarChart3 },
      { path: 'usuarios', label: 'Usuarios y socios', order: 10, icon: Users },
      { path: 'recursos', label: 'Buscar recursos', order: 60, icon: Search },
    ],
  },
})
