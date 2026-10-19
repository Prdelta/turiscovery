import { lazy, Suspense, type ComponentType, type ReactNode } from 'react'
import { createBrowserRouter, Navigate } from 'react-router'
import { PanelLayout, RequireAuth } from './components/layout/PanelLayout'
import { SiteLayout } from './components/layout/SiteLayout'
import { Spinner } from './components/ui/Feedback'
import Home from './pages/Home'
import { NotFoundPage } from './pages/NotFoundPage'

/** Carga diferida de páginas para que la portada no descargue los paneles */
function page<T extends Record<string, ComponentType<never>>>(loader: () => Promise<T>, name: keyof T, props?: object): ReactNode {
  const Component = lazy(async () => ({ default: (await loader())[name] as ComponentType }))
  return (
    <Suspense fallback={<Spinner />}>
      <Component {...(props as object)} />
    </Suspense>
  )
}

const details = () => import('./pages/Details')
const candelaria = () => import('./pages/CandelariaPage')
const auth = () => import('./pages/AuthPages')
const statics = () => import('./pages/StaticPages')
const user = () => import('./pages/user/UserPages')
const dash = () => import('./pages/dashboard/DashboardPages')
const admin = () => import('./pages/admin/AdminPages')
const list = () => import('./pages/ListPage')

export const router = createBrowserRouter([
  {
    element: <SiteLayout />,
    children: [
      { path: '/', element: <Home /> },

      { path: '/candelaria', element: page(candelaria, 'default') },
      { path: '/candelaria/danzas/:id', element: page(candelaria, 'DanzaDetail') },
      { path: '/candelaria/publicaciones/:id', element: page(details, 'CandelariaPostDetail') },

      { path: '/experiencias', element: page(list, 'default', { type: 'experiencias' }) },
      { path: '/experiencias/:id', element: page(details, 'ExperienciaDetail') },
      { path: '/eventos', element: page(list, 'default', { type: 'eventos' }) },
      { path: '/eventos/:id', element: page(details, 'EventoDetail') },
      { path: '/promociones', element: page(list, 'default', { type: 'promociones' }) },
      { path: '/promociones/:id', element: page(details, 'PromocionDetail') },
      { path: '/locales', element: page(list, 'default', { type: 'locales' }) },
      { path: '/locales/:id', element: page(details, 'LocaleDetail') },

      { path: '/buscar', element: page(() => import('./pages/SearchPage'), 'default') },
      { path: '/search', element: <Navigate to="/buscar" replace /> },
      { path: '/mapa', element: page(() => import('./pages/MapPage'), 'default') },
      { path: '/puno-3d', element: page(() => import('./pages/Puno3DPage'), 'default') },

      { path: '/login', element: page(auth, 'LoginPage') },
      { path: '/registro', element: page(auth, 'RegisterPage') },
      { path: '/register', element: <Navigate to="/registro" replace /> },

      { path: '/sobre-nosotros', element: page(statics, 'AboutPage') },
      { path: '/contacto', element: page(statics, 'ContactPage') },
      { path: '/terminos', element: page(statics, 'TermsPage') },
      { path: '/privacidad', element: page(statics, 'PrivacyPage') },

      {
        path: '/cuenta',
        element: (
          <RequireAuth>
            <PanelLayout section="cuenta" />
          </RequireAuth>
        ),
        children: [
          { index: true, element: page(user, 'AccountHome') },
          { path: 'favoritos', element: page(user, 'FavoritesPage') },
          { path: 'reservas', element: page(user, 'BookingsPage') },
          { path: 'eventos', element: page(user, 'AttendancesPage') },
          { path: 'cupones', element: page(user, 'CouponsPage') },
          { path: 'resenas', element: page(user, 'MyReviewsPage') },
          { path: 'perfil', element: page(user, 'ProfilePage') },
        ],
      },
      { path: '/user/*', element: <Navigate to="/cuenta" replace /> },

      {
        path: '/dashboard',
        element: (
          <RequireAuth roles={['socio', 'admin']}>
            <PanelLayout section="dashboard" />
          </RequireAuth>
        ),
        children: [
          { index: true, element: page(dash, 'DashboardHome') },
          { path: 'reservas', element: page(dash, 'PartnerBookingsPage') },
          { path: ':type', element: page(dash, 'ContentManager') },
          { path: ':type/nuevo', element: page(dash, 'ContentFormPage') },
          { path: ':type/:id', element: page(dash, 'ContentFormPage') },
        ],
      },

      {
        path: '/admin',
        element: (
          <RequireAuth roles={['admin']}>
            <PanelLayout section="admin" />
          </RequireAuth>
        ),
        children: [
          { index: true, element: page(admin, 'AdminHome') },
          { path: 'usuarios', element: page(admin, 'UsersPage') },
          { path: 'usuarios/:id', element: page(admin, 'UserFormPage') },
          { path: 'danzas', element: page(admin, 'DanzasAdmin') },
          { path: 'galeria', element: page(admin, 'GalleryAdmin') },
          { path: 'recursos', element: page(admin, 'ResourceSearchPage') },
        ],
      },

      { path: '*', element: <NotFoundPage /> },
    ],
  },
])
