import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import {
  BarChart3,
  CalendarDays,
  Compass,
  Heart,
  Home,
  Images,
  LogOut,
  Music2,
  Radio,
  Search,
  Settings,
  Star,
  Sun,
  Ticket,
  UserCog,
  Users,
  type LucideIcon,
} from 'lucide-react'
import type { ReactNode } from 'react'
import { Navigate, NavLink, Outlet, useLocation, useNavigate } from 'react-router'
import { useAuth } from '../../auth/AuthContext'
import { getData, type Paginated } from '../../lib/api'
import type { Role } from '../../lib/types'
import { Tocapu, type Dye, type Motif } from '../textile/Weave'
import { ButtonLink } from '../ui/Button'
import { Spinner, StatePanel } from '../ui/Feedback'
import { Avatar } from './SiteLayout'

interface NavItem {
  to: string
  label: string
  /** Ícono de interfaz (Lucide) o, para tipos de contenido, su motivo tejido */
  icon?: LucideIcon
  motif?: [Motif, Dye]
  end?: boolean
  /** Contador a la derecha (por ejemplo, reservas pendientes) */
  countKey?: 'pendingBookings'
}

type SectionKey = 'cuenta' | 'dashboard' | 'admin'

const SECTIONS: Record<SectionKey, { title: string; items: NavItem[] }> = {
  cuenta: {
    title: 'Mi cuenta',
    items: [
      { to: '/cuenta', label: 'Resumen', icon: Home, end: true },
      { to: '/cuenta/favoritos', label: 'Favoritos', icon: Heart },
      { to: '/cuenta/reservas', label: 'Reservas', icon: Compass },
      { to: '/cuenta/eventos', label: 'Eventos', icon: CalendarDays },
      { to: '/cuenta/cupones', label: 'Cupones', icon: Ticket },
      { to: '/cuenta/resenas', label: 'Reseñas', icon: Star },
      { to: '/cuenta/perfil', label: 'Perfil', icon: Settings },
    ],
  },
  dashboard: {
    title: 'Panel de socio',
    items: [
      { to: '/dashboard', label: 'Resumen', icon: BarChart3, end: true },
      { to: '/dashboard/reservas', label: 'Reservas', icon: CalendarDays, countKey: 'pendingBookings' },
      { to: '/dashboard/experiencias', label: 'Experiencias', motif: ['olas', 'anil'] },
      { to: '/dashboard/eventos', label: 'Eventos', motif: ['chakana', 'cochinilla'] },
      { to: '/dashboard/promociones', label: 'Promociones', motif: ['ojo', 'chilca'] },
      { to: '/dashboard/locales', label: 'Locales', motif: ['llave', 'tierra'] },
      { to: '/dashboard/anfitriones', label: 'Anfitriones', motif: ['persona', 'qolle'] },
      { to: '/dashboard/candelaria', label: 'Candelaria', motif: ['estrella', 'morado'] },
    ],
  },
  admin: {
    title: 'Administración',
    items: [
      { to: '/admin', label: 'Supervisión', icon: BarChart3, end: true },
      { to: '/admin/usuarios', label: 'Usuarios y socios', icon: Users },
      { to: '/admin/almanaque', label: 'Almanaque', icon: Sun },
      { to: '/admin/candelaria-vivo', label: 'Candelaria en vivo', icon: Radio },
      { to: '/admin/danzas', label: 'Danzas', icon: Music2 },
      { to: '/admin/galeria', label: 'Galería Candelaria', icon: Images },
      { to: '/admin/recursos', label: 'Buscar recursos', icon: Search },
    ],
  },
}

/** Protege rutas por sesión y rol (con el estado "sin permisos" de la guía) */
export function RequireAuth({ roles, children }: { roles?: Role[]; children: ReactNode }) {
  const { user, loading } = useAuth()
  const location = useLocation()
  if (loading) return <Spinner label="Comprobando tu sesión…" />
  if (!user) return <Navigate to={`/login?next=${encodeURIComponent(location.pathname)}`} replace />
  if (roles && !roles.includes(user.role)) {
    const forPartners = roles.includes('socio')
    return (
      <div className="container-page py-16">
        <StatePanel
          kind="forbidden"
          title={forPartners ? 'Esta sección es para socios' : 'Esta sección es para administradores'}
          action={forPartners ? <ButtonLink to="/contacto" variant="secondary">Ser socio</ButtonLink> : <ButtonLink to="/" variant="secondary">Volver al inicio</ButtonLink>}
        >
          {forPartners
            ? 'Si tienes un negocio en Puno, solicita tu cuenta de socio.'
            : 'Si crees que deberías tener acceso, pide a un administrador que revise tu rol.'}
        </StatePanel>
      </div>
    )
  }
  return <>{children}</>
}

function ItemIcon({ item, size = 20 }: { item: NavItem; size?: number }) {
  if (item.motif) {
    return (
      <span className="inline-flex shrink-0 items-center justify-center rounded-[6px] bg-lana p-0.5">
        <Tocapu motif={item.motif[0]} dye={item.motif[1]} size={size - 4} framed={false} />
      </span>
    )
  }
  const Icon = item.icon!
  return <Icon className="shrink-0" style={{ width: size, height: size }} aria-hidden />
}

/** Contadores del panel (reservas por confirmar) */
function usePanelCounts(enabled: boolean) {
  const { data } = useQuery({
    queryKey: ['partner-bookings', 'pending', 1],
    enabled,
    queryFn: () => getData<Paginated<unknown>>('/partner/bookings', { status: 'pending' }),
    staleTime: 60_000,
  })
  return { pendingBookings: data?.total ?? 0 }
}

/** Pestañas en chips: Mi cuenta en todos los tamaños y los paneles en móvil */
function ChipTabs({ items, counts, className }: { items: NavItem[]; counts: Record<string, number>; className?: string }) {
  return (
    <nav aria-label="Secciones" className={clsx('-mx-4 overflow-x-auto px-4 pb-1', className)}>
      <ul className="flex w-max gap-2">
        {items.map((item) => (
          <li key={item.to}>
            <NavLink
              to={item.to}
              end={item.end}
              className={({ isActive }) =>
                clsx(
                  'flex min-h-11 items-center gap-2 whitespace-nowrap rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors',
                  isActive ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky',
                )
              }
            >
              {item.label}
              {item.countKey && counts[item.countKey] > 0 && (
                <span className="rounded-full bg-sun px-1.5 text-xs text-ink">{counts[item.countKey]}</span>
              )}
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  )
}

export function PanelLayout({ section }: { section: SectionKey }) {
  const { user, logout, hasRole } = useAuth()
  const navigate = useNavigate()
  const isPartnerArea = section !== 'cuenta'
  const counts = usePanelCounts(isPartnerArea && hasRole('socio', 'admin'))

  // Mi cuenta: encabezado y pestañas en chips, sin panel lateral
  if (!isPartnerArea) {
    return (
      <div className="container-page py-8 lg:py-12">
        <div className="mb-6 flex items-center gap-3">
          {user && <Avatar name={user.name} src={user.avatar} />}
          <p className="t-h2">Mi cuenta</p>
        </div>
        <ChipTabs items={SECTIONS.cuenta.items} counts={counts} className="mb-8" />
        <Outlet />
      </div>
    )
  }

  const groups = [SECTIONS[section]]
  // Los admins también gestionan contenido como socios
  if (section === 'admin') groups.push(SECTIONS.dashboard)
  if (section === 'dashboard' && hasRole('admin')) groups.push(SECTIONS.admin)

  return (
    <div className="container-page grid gap-8 py-8 lg:grid-cols-[264px_1fr] lg:py-12">
      {/* Móvil: chips con las secciones del panel */}
      <div className="lg:hidden">
        <ChipTabs items={groups.flatMap((g) => g.items)} counts={counts} />
      </div>

      {/* Escritorio: panel lateral de tinta */}
      <aside className="hidden lg:sticky lg:top-24 lg:block lg:self-start">
        <div className="rounded-[var(--radius-panel)] bg-ink p-4 text-white">
          {user && (
            <div className="mb-4 flex items-center gap-3 px-2 pt-1">
              <Tocapu motif="llave" dye="qolle" size={28} framed={false} />
              <div className="min-w-0">
                <p className="truncate font-display text-lg font-extrabold text-white">{user.name}</p>
                <p className="text-xs text-white/70">{{ tourist: 'Viajero', socio: 'Socio', admin: 'Administrador' }[user.role]}</p>
              </div>
            </div>
          )}
          {groups.map((group) => (
            <nav key={group.title} aria-label={group.title} className="mb-4 last:mb-2">
              <h2 className="t-label mb-2 px-2 text-white/60">{group.title}</h2>
              <ul className="flex flex-col gap-1">
                {group.items.map((item) => (
                  <li key={item.to}>
                    <NavLink
                      to={item.to}
                      end={item.end}
                      className={({ isActive }) =>
                        clsx(
                          'flex min-h-11 items-center gap-3 rounded-[var(--radius-control)] px-3 text-[0.95rem] font-bold transition-colors',
                          isActive ? 'bg-lana text-ink' : 'text-white hover:bg-white/10',
                        )
                      }
                    >
                      <ItemIcon item={item} />
                      <span className="flex-1">{item.label}</span>
                      {item.countKey && counts[item.countKey] > 0 && (
                        <span className="rounded-full bg-sun px-2 text-xs font-extrabold text-ink" aria-label={`${counts[item.countKey]} pendientes`}>
                          {counts[item.countKey]}
                        </span>
                      )}
                    </NavLink>
                  </li>
                ))}
              </ul>
            </nav>
          ))}
          <div className="flex flex-col gap-1 border-t border-white/15 pt-3">
            <NavLink to="/cuenta/perfil" className="flex min-h-11 items-center gap-3 rounded-[var(--radius-control)] px-3 text-[0.95rem] font-bold text-white hover:bg-white/10">
              <UserCog className="size-5" aria-hidden /> Mi perfil
            </NavLink>
            <button
              onClick={async () => {
                await logout()
                navigate('/')
              }}
              className="flex min-h-11 items-center gap-3 rounded-[var(--radius-control)] px-3 text-left text-[0.95rem] font-bold text-white hover:bg-white/10"
            >
              <LogOut className="size-5" aria-hidden /> Cerrar sesión
            </button>
          </div>
        </div>
      </aside>
      <div className="min-w-0">
        <Outlet />
      </div>
    </div>
  )
}

export function PageHeader({ title, description, action }: { title: string; description?: string; action?: ReactNode }) {
  return (
    <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h1 className="t-h1">{title}</h1>
        {description && <p className="prose-measure mt-2 text-ink-soft">{description}</p>}
      </div>
      {action}
    </div>
  )
}
