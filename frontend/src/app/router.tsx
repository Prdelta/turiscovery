import { createBrowserRouter, Navigate, type RouteObject } from 'react-router'
import { FEATURES, PANELS } from '@/app/features'
import { PanelLayout } from '@/app/layouts/PanelLayout'
import { SiteLayout } from '@/app/layouts/SiteLayout'
import { RequireAuth } from '@/features/auth/RequireAuth'
import { NotFoundPage } from '@/features/info/NotFoundPage'
import type { PanelSection } from '@/shared/routing/feature'

/** Rutas de un panel: las que aporta cada feature, bajo su layout y su control de acceso */
function panelRoute(section: PanelSection): RouteObject {
  const { base, roles } = PANELS[section]
  return {
    path: base,
    element: (
      <RequireAuth roles={roles}>
        <PanelLayout section={section} />
      </RequireAuth>
    ),
    children: FEATURES.flatMap((f) => f.panels?.[section] ?? []),
  }
}

export const router = createBrowserRouter([
  {
    element: <SiteLayout />,
    children: [
      ...FEATURES.flatMap((f) => f.site ?? []),
      panelRoute('account'),
      panelRoute('partner'),
      panelRoute('admin'),
      // Direcciones antiguas
      { path: '/user/*', element: <Navigate to="/cuenta" replace /> },
      { path: '*', element: <NotFoundPage /> },
    ],
  },
])
