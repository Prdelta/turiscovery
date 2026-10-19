import { clsx } from 'clsx'
import {
  BarChart3,
  CalendarDays,
  Compass,
  Heart,
  Home,
  Images,
  LogOut,
  Megaphone,
  Music2,
  Radio,
  Search,
  Settings,
  Sparkles,
  Star,
  Store,
  Ticket,
  UserCog,
  Users,
  type LucideIcon,
} from 'lucide-react'
import { Link, Navigate, NavLink, Outlet, useLocation, useNavigate } from 'react-router'
import { useAuth } from '../../auth/AuthContext'
import type { Role } from '../../lib/types'
import { Spinner } from '../ui/Feedback'
import { Avatar } from './SiteLayout'

interface NavItem {
  to: string
  label: string
  icon: LucideIcon
  end?: boolean
}

const SECTIONS: Record<'cuenta' | 'dashboard' | 'admin', { title: string; items: NavItem[] }> = {
  cuenta: {
    title: 'Mi cuenta',
    items: [
      { to: '/cuenta', label: 'Resumen', icon: Home, end: true },
      { to: '/cuenta/favoritos', label: 'Favoritos', icon: Heart },
      { to: '/cuenta/reservas', label: 'Reservas', icon: Compass },
      { to: '/cuenta/eventos', label: 'Asistencias', icon: CalendarDays },
      { to: '/cuenta/cupones', label: 'Cupones', icon: Ticket },
      { to: '/cuenta/resenas', label: 'Reseñas', icon: Star },
      { to: '/cuenta/perfil', label: 'Perfil', icon: Settings },
    ],
  },
  dashboard: {
    title: 'Panel de socio',
    items: [
      { to: '/dashboard', label: 'Resumen', icon: BarChart3, end: true },
      { to: '/dashboard/reservas', label: 'Reservas recibidas', icon: Ticket },
      { to: '/dashboard/locales', label: 'Locales', icon: Store },
      { to: '/dashboard/experiencias', label: 'Experiencias', icon: Compass },
      { to: '/dashboard/eventos', label: 'Eventos', icon: CalendarDays },
      { to: '/dashboard/promociones', label: 'Promociones', icon: Megaphone },
      { to: '/dashboard/candelaria', label: 'Candelaria', icon: Sparkles },
    ],
  },
  admin: {
    title: 'Administración',
    items: [
      { to: '/admin', label: 'Supervisión', icon: BarChart3, end: true },
      { to: '/admin/usuarios', label: 'Usuarios y socios', icon: Users },
      { to: '/admin/candelaria-vivo', label: 'Candelaria en vivo', icon: Radio },
      { to: '/admin/danzas', label: 'Danzas', icon: Music2 },
      { to: '/admin/galeria', label: 'Galería Candelaria', icon: Images },
      { to: '/admin/recursos', label: 'Buscar recursos', icon: Search },
    ],
  },
}

/** Protege rutas por sesión y rol */
export function RequireAuth({ roles, children }: { roles?: Role[]; children: React.ReactNode }) {
  const { user, loading } = useAuth()
  const location = useLocation()
  if (loading) return <Spinner label="Comprobando tu sesión…" />
  if (!user) return <Navigate to={`/login?next=${encodeURIComponent(location.pathname)}`} replace />
  if (roles && !roles.includes(user.role)) {
    return (
      <div className="container-page py-24 text-center">
        <h1 className="text-3xl font-bold">Esta sección no está disponible para tu cuenta</h1>
        <p className="mt-3 text-ink-soft">Si crees que deberías tener acceso, pide a un administrador que revise tu rol.</p>
        <Link to="/" className="mt-6 inline-block font-semibold text-primary">Volver al inicio</Link>
      </div>
    )
  }
  return <>{children}</>
}

export function PanelLayout({ section }: { section: keyof typeof SECTIONS }) {
  const { user, logout, hasRole } = useAuth()
  const navigate = useNavigate()
  const groups = [SECTIONS[section]]
  // Los admins también gestionan contenido como socios
  if (section === 'admin') groups.push(SECTIONS.dashboard)
  if (section === 'dashboard' && hasRole('admin')) groups.push(SECTIONS.admin)

  return (
    <div className="container-page grid gap-8 py-8 lg:grid-cols-[250px_1fr] lg:py-12">
      <aside className="lg:sticky lg:top-24 lg:self-start">
        {user && (
          <div className="mb-5 flex items-center gap-3 rounded-2xl bg-white p-3">
            <Avatar name={user.name} src={user.avatar} />
            <div className="min-w-0">
              <p className="truncate font-semibold">{user.name}</p>
              <p className="text-xs text-ink-soft">{{ tourist: 'Viajero', socio: 'Socio', admin: 'Administrador' }[user.role]}</p>
            </div>
          </div>
        )}
        {groups.map((group) => (
          <nav key={group.title} aria-label={group.title} className="mb-5">
            <h2 className="mb-2 px-3 text-sm font-semibold text-ink-soft">{group.title}</h2>
            <ul className="flex gap-1 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
              {group.items.map((item) => (
                <li key={item.to} className="shrink-0">
                  <NavLink
                    to={item.to}
                    end={item.end}
                    className={({ isActive }) =>
                      clsx(
                        'flex items-center gap-2.5 rounded-xl px-3 py-2 text-[0.93rem] font-medium transition-colors',
                        isActive ? 'bg-primary text-white' : 'hover:bg-white',
                      )
                    }
                  >
                    <item.icon className="size-4.5" aria-hidden />
                    {item.label}
                  </NavLink>
                </li>
              ))}
            </ul>
          </nav>
        ))}
        <div className="hidden flex-col gap-1 border-t border-line pt-4 lg:flex">
          {section !== 'cuenta' && (
            <NavLink to="/cuenta/perfil" className="flex items-center gap-2.5 rounded-xl px-3 py-2 text-[0.93rem] font-medium hover:bg-white">
              <UserCog className="size-4.5" aria-hidden /> Mi perfil
            </NavLink>
          )}
          <button
            onClick={async () => {
              await logout()
              navigate('/')
            }}
            className="flex items-center gap-2.5 rounded-xl px-3 py-2 text-left text-[0.93rem] font-medium hover:bg-white"
          >
            <LogOut className="size-4.5" aria-hidden /> Cerrar sesión
          </button>
        </div>
      </aside>
      <div className="min-w-0">
        <Outlet />
      </div>
    </div>
  )
}

export function PageHeader({ title, description, action }: { title: string; description?: string; action?: React.ReactNode }) {
  return (
    <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h1 className="text-3xl font-extrabold sm:text-4xl">{title}</h1>
        {description && <p className="mt-2 max-w-2xl text-ink-soft">{description}</p>}
      </div>
      {action}
    </div>
  )
}
