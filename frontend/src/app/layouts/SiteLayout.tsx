import { clsx } from 'clsx'
import { Bookmark, Home, LogOut, MapPin, Menu, Search, User as UserIcon, X } from 'lucide-react'
import { useState } from 'react'
import { Link, NavLink, Outlet, ScrollRestoration, useLocation, useNavigate } from 'react-router'
import { NotificationBell } from '@/features/notifications/NotificationBell'
import { homeForRole, useAuth } from '@/shared/auth/AuthContext'
import { InstallAppButton, OfflineBanner } from '@/shared/offline/OfflineTools'
import { SiteBand, StitchRow } from '@/shared/textile/Weave'
import { Avatar, Brand } from '@/shared/ui/Identity'
import { ButtonLink } from '@/shared/ui/Button'

/** Enlaces del encabezado de escritorio y del menú secundario en móvil */
const NAV = [
  { to: '/candelaria', label: 'Candelaria' },
  { to: '/experiencias', label: 'Experiencias' },
  { to: '/eventos', label: 'Eventos' },
  { to: '/anfitriones', label: 'Anfitriones' },
  { to: '/hoy', label: 'Hoy' },
  { to: '/planificador', label: 'Planifica' },
  { to: '/mapa', label: 'Mapa' },
]

const MORE = [
  { to: '/promociones', label: 'Promociones' },
  { to: '/locales', label: 'Locales' },
  { to: '/puno-3d', label: 'Modelos 3D' },
]

export function SiteLayout() {
  return (
    <div className="flex min-h-dvh flex-col">
      <a href="#contenido" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[3000] focus:rounded-[var(--radius-control)] focus:bg-white focus:px-4 focus:py-2">
        Saltar al contenido
      </a>
      <Header />
      <OfflineBanner />
      {/* En móvil se deja espacio para la barra inferior */}
      <main id="contenido" className="flex-1 pb-20 lg:pb-0">
        <Outlet />
      </main>
      <Footer />
      <BottomNav />
      <ScrollRestoration />
    </div>
  )
}

function Header() {
  const { user, logout } = useAuth()
  // El menú queda abierto solo en la ruta donde se abrió: al navegar se cierra
  const [openAt, setOpenAt] = useState<string | null>(null)
  const location = useLocation()
  const navigate = useNavigate()

  const open = openAt === location.pathname

  const signOut = async () => {
    await logout()
    navigate('/')
  }

  return (
    <header className="sticky top-0 z-[1000] bg-lana">
      <div className="container-page flex h-16 items-center gap-4">
        <Link to="/" aria-label="Ñan Puno, inicio" className="shrink-0">
          <Brand />
        </Link>

        <nav aria-label="Principal" className="ml-4 hidden flex-1 items-center gap-0.5 lg:flex">
          {NAV.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              className={({ isActive }) =>
                clsx(
                  'whitespace-nowrap rounded-[var(--radius-control)] px-3 py-2 text-[0.95rem] font-bold transition-colors',
                  isActive ? 'bg-ink text-white' : 'text-ink hover:bg-white',
                )
              }
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="ml-auto flex items-center gap-2">
          <Link to="/buscar" aria-label="Buscar" className="hidden size-11 items-center justify-center rounded-[var(--radius-control)] hover:bg-white lg:inline-flex">
            <Search className="size-5" />
          </Link>
          <NotificationBell />
          {user ? (
            <div className="hidden items-center gap-1 lg:flex">
              <Link to={homeForRole(user.role)} className="flex min-h-11 items-center gap-2 rounded-[var(--radius-control)] py-1 pl-1 pr-3 text-sm font-bold hover:bg-white">
                <Avatar name={user.name} src={user.avatar} />
                <span className="max-w-32 truncate">{user.name.split(' ')[0]}</span>
              </Link>
              <button onClick={signOut} aria-label="Cerrar sesión" className="inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] hover:bg-white">
                <LogOut className="size-5" />
              </button>
            </div>
          ) : (
            <div className="hidden items-center gap-2 lg:flex">
              <ButtonLink to="/login" variant="ghost" size="sm">Iniciar sesión</ButtonLink>
              <ButtonLink to="/registro" size="sm">Crear cuenta</ButtonLink>
            </div>
          )}
          <button
            className="inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white lg:hidden"
            onClick={() => setOpenAt(open ? null : location.pathname)}
            aria-expanded={open}
            aria-controls="menu-movil"
            aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
          >
            {open ? <X className="size-5" /> : <Menu className="size-5" />}
          </button>
        </div>
      </div>
      <StitchRow dye="cochinilla" />

      {open && (
        <nav id="menu-movil" aria-label="Menú" className="border-b-2 border-ink bg-lana lg:hidden">
          <ul className="container-page grid grid-cols-2 gap-2 py-4">
            {[...NAV, ...MORE].map((item) => (
              <li key={item.to}>
                <NavLink
                  to={item.to}
                  className={({ isActive }) =>
                    clsx(
                      'flex min-h-12 items-center rounded-[var(--radius-control)] border-2 px-3 font-bold',
                      isActive ? 'border-ink bg-ink text-white' : 'border-ink bg-white',
                    )
                  }
                >
                  {item.label}
                </NavLink>
              </li>
            ))}
          </ul>
          <div className="container-page flex flex-wrap gap-2 pb-4">
            {user ? (
              <button onClick={signOut} className="flex min-h-11 items-center gap-2 font-bold text-primary-dark underline decoration-2 underline-offset-4">
                <LogOut className="size-4" /> Cerrar sesión
              </button>
            ) : (
              <>
                <ButtonLink to="/registro" fullWidth>Crear cuenta</ButtonLink>
                <ButtonLink to="/login" variant="secondary" fullWidth>Iniciar sesión</ButtonLink>
              </>
            )}
          </div>
        </nav>
      )}
    </header>
  )
}

/** Barra inferior en móvil: "Viaje" agrupa lo guardado sin conexión, el plan y los favoritos */
function BottomNav() {
  const { user } = useAuth()
  const items = [
    { to: '/', label: 'Inicio', icon: Home, end: true },
    { to: '/buscar', label: 'Buscar', icon: Search },
    { to: '/mapa', label: 'Mapa', icon: MapPin },
    { to: '/viaje', label: 'Viaje', icon: Bookmark },
    { to: user ? homeForRole(user.role) : '/login', label: 'Cuenta', icon: UserIcon },
  ]
  return (
    <nav aria-label="Navegación inferior" className="fixed inset-x-0 bottom-0 z-[1100] border-t-2 border-ink bg-lana pb-[env(safe-area-inset-bottom)] lg:hidden">
      <ul className="grid grid-cols-5">
        {items.map(({ to, label, icon: Icon, end }) => (
          <li key={label}>
            <NavLink
              to={to}
              end={end}
              className={({ isActive }) =>
                clsx(
                  'relative flex min-h-16 flex-col items-center justify-center gap-0.5 text-xs',
                  isActive ? 'font-extrabold text-ink' : 'font-semibold text-ink-soft',
                )
              }
            >
              {({ isActive }) => (
                <>
                  {isActive && <span className="absolute inset-x-3 top-0 h-[3px] rounded-b bg-cochinilla" aria-hidden />}
                  <Icon className="size-6" strokeWidth={isActive ? 2.5 : 2} aria-hidden />
                  {label}
                </>
              )}
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  )
}

const CURRENT_YEAR = new Date().getFullYear()

function Footer() {
  return (
    <footer className="bg-ink pb-20 text-white/80 lg:pb-0">
      <SiteBand />
      <div className="container-page grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div className="flex flex-col gap-3">
          <Brand dark />
          <p className="max-w-xs text-sm">
            Guía de Puno hecha con quienes viven aquí: socios locales publican experiencias, eventos y promociones reales.
          </p>
          <InstallAppButton className="mt-2 self-start" />
        </div>
        <FooterCol title="Descubre" links={[['/candelaria', 'Candelaria'], ['/experiencias', 'Experiencias'], ['/eventos', 'Eventos'], ['/promociones', 'Promociones']]} />
        <FooterCol title="Explora" links={[['/hoy', 'Almanaque'], ['/planificador', 'Planifica tu viaje'], ['/anfitriones', 'Anfitriones'], ['/locales', 'Locales'], ['/mapa', 'Mapa'], ['/puno-3d', 'Modelos 3D']]} />
        <FooterCol title="Ñan Puno" links={[['/sobre-nosotros', 'Sobre nosotros'], ['/contacto', 'Contacto'], ['/terminos', 'Términos y condiciones'], ['/privacidad', 'Política de privacidad']]} />
      </div>
      <div className="border-t border-white/15">
        <p className="container-page py-5 text-xs text-white/60">© {CURRENT_YEAR} Ñan Puno · Perú</p>
      </div>
    </footer>
  )
}

function FooterCol({ title, links }: { title: string; links: [string, string][] }) {
  return (
    <div>
      <h2 className="mb-3 font-display text-base font-bold text-white">{title}</h2>
      <ul className="flex flex-col gap-2 text-sm">
        {links.map(([to, label]) => (
          <li key={to}>
            <Link to={to} className="hover:text-mint">{label}</Link>
          </li>
        ))}
      </ul>
    </div>
  )
}
