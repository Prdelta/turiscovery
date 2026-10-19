import { clsx } from 'clsx'
import { LogOut, Menu, Search, User as UserIcon, X } from 'lucide-react'
import { useEffect, useState } from 'react'
import { Link, NavLink, Outlet, ScrollRestoration, useLocation, useNavigate } from 'react-router'
import { homeForRole, useAuth } from '../../auth/AuthContext'
import { initials } from '../../lib/format'
import { InstallAppButton, OfflineBanner } from '../OfflineTools'
import { SiteBand } from '../textile/Weave'
import { ButtonLink } from '../ui/Button'

const NAV = [
  { to: '/candelaria', label: 'Candelaria' },
  { to: '/experiencias', label: 'Experiencias' },
  { to: '/anfitriones', label: 'Anfitriones' },
  { to: '/eventos', label: 'Eventos' },
  { to: '/promociones', label: 'Promociones' },
  { to: '/hoy', label: 'Hoy' },
  { to: '/planificador', label: 'Planifica' },
  { to: '/mapa', label: 'Mapa' },
]

export function SiteLayout() {
  const location = useLocation()
  // La portada arranca con el héroe 3D a pantalla completa: barra transparente encima
  const overlay = location.pathname === '/'

  return (
    <div className="flex min-h-dvh flex-col">
      <a href="#contenido" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[3000] focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
        Saltar al contenido
      </a>
      <Navbar overlay={overlay} />
      <OfflineBanner />
      <main id="contenido" className="flex-1">
        <Outlet />
      </main>
      <Footer />
      <ScrollRestoration />
    </div>
  )
}

function Navbar({ overlay }: { overlay: boolean }) {
  const { user, logout } = useAuth()
  const [open, setOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const location = useLocation()
  const navigate = useNavigate()

  useEffect(() => setOpen(false), [location.pathname])

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40)
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  const dark = overlay && !scrolled && !open

  return (
    <header
      className={clsx(
        'top-0 z-[1000] w-full transition-colors',
        overlay ? 'fixed' : 'sticky',
        dark ? 'bg-lake-deep/35 text-white backdrop-blur-sm' : 'border-b border-line bg-white/95 text-ink backdrop-blur',
      )}
    >
      <div className="container-page flex h-16 items-center gap-6">
        <Link to="/" className="flex shrink-0 items-center gap-2.5" aria-label="Turiscovery, inicio">
          <img src="/logo.png" alt="" className="size-9 rounded-lg" />
          <span className="font-display text-xl font-extrabold tracking-tight">Turiscovery</span>
        </Link>

        <nav aria-label="Principal" className="ml-2 hidden flex-1 items-center gap-0.5 xl:flex">
          {NAV.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              className={({ isActive }) =>
                clsx(
                  'whitespace-nowrap rounded-full px-3 py-1.5 text-[0.92rem] font-medium transition-colors',
                  isActive
                    ? dark ? 'bg-white/15' : 'bg-primary/10 text-primary'
                    : dark ? 'hover:bg-white/10' : 'hover:bg-ink/5',
                )
              }
            >
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="ml-auto flex items-center gap-2">
          <Link
            to="/buscar"
            aria-label="Buscar"
            className={clsx('inline-flex size-10 items-center justify-center rounded-full', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}
          >
            <Search className="size-5" />
          </Link>

          {user ? (
            <div className="hidden items-center gap-2 sm:flex">
              <Link
                to={homeForRole(user.role)}
                className={clsx('flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm font-semibold', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}
              >
                <Avatar name={user.name} src={user.avatar} />
                <span className="max-w-32 truncate">{user.name.split(' ')[0]}</span>
              </Link>
              <button
                onClick={async () => {
                  await logout()
                  navigate('/')
                }}
                aria-label="Cerrar sesión"
                className={clsx('inline-flex size-10 items-center justify-center rounded-full', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}
              >
                <LogOut className="size-4.5" />
              </button>
            </div>
          ) : (
            <div className="hidden items-center gap-2 sm:flex">
              <Link to="/login" className={clsx('whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}>
                Iniciar sesión
              </Link>
              <ButtonLink to="/registro" size="sm" variant={dark ? 'mint' : 'primary'}>
                Crear cuenta
              </ButtonLink>
            </div>
          )}

          <button
            className={clsx('inline-flex size-10 items-center justify-center rounded-full xl:hidden', dark ? 'hover:bg-white/10' : 'hover:bg-ink/5')}
            onClick={() => setOpen((o) => !o)}
            aria-expanded={open}
            aria-controls="menu-movil"
            aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
          >
            {open ? <X className="size-5" /> : <Menu className="size-5" />}
          </button>
        </div>
      </div>

      {open && (
        <nav id="menu-movil" aria-label="Menú móvil" className="border-t border-line bg-white text-ink xl:hidden">
          <div className="container-page flex flex-col py-3">
            {NAV.map((item) => (
              <NavLink key={item.to} to={item.to} className="rounded-xl px-3 py-3 text-lg font-medium hover:bg-ink/5">
                {item.label}
              </NavLink>
            ))}
            <div className="mt-2 flex flex-col gap-2 border-t border-line pt-4">
              {user ? (
                <>
                  <Link to={homeForRole(user.role)} className="flex items-center gap-3 rounded-xl px-3 py-2 font-semibold hover:bg-ink/5">
                    <UserIcon className="size-5" /> Mi cuenta
                  </Link>
                  <button
                    onClick={async () => {
                      await logout()
                      navigate('/')
                    }}
                    className="flex items-center gap-3 rounded-xl px-3 py-2 text-left font-semibold hover:bg-ink/5"
                  >
                    <LogOut className="size-5" /> Cerrar sesión
                  </button>
                </>
              ) : (
                <>
                  <ButtonLink to="/registro">Crear cuenta</ButtonLink>
                  <ButtonLink to="/login" variant="secondary">Iniciar sesión</ButtonLink>
                </>
              )}
            </div>
          </div>
        </nav>
      )}
    </header>
  )
}

export function Avatar({ name, src, size = 'sm' }: { name: string; src?: string | null; size?: 'sm' | 'lg' }) {
  const cls = size === 'sm' ? 'size-8 text-xs' : 'size-16 text-xl'
  if (src) return <img src={src} alt="" className={clsx(cls, 'rounded-full object-cover')} referrerPolicy="no-referrer" />
  return (
    <span className={clsx(cls, 'inline-flex items-center justify-center rounded-full bg-primary font-bold text-white')} aria-hidden>
      {initials(name)}
    </span>
  )
}

function Footer() {
  return (
    <footer className="bg-lake-deep text-white/80">
      <SiteBand />
      <div className="container-page grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div className="flex flex-col gap-3">
          <div className="flex items-center gap-2.5 text-white">
            <img src="/logo.png" alt="" className="size-9 rounded-lg" />
            <span className="font-display text-xl font-extrabold">Turiscovery</span>
          </div>
          <p className="max-w-xs text-sm">
            Guía de Puno hecha con quienes viven aquí: socios locales publican experiencias, eventos y promociones reales.
          </p>
          <InstallAppButton className="mt-2 self-start" />
        </div>
        <FooterCol title="Descubre" links={[['/candelaria', 'Candelaria'], ['/experiencias', 'Experiencias'], ['/eventos', 'Eventos'], ['/promociones', 'Promociones']]} />
        <FooterCol title="Explora" links={[['/hoy', 'Almanaque'], ['/planificador', 'Planifica tu viaje'], ['/anfitriones', 'Anfitriones'], ['/locales', 'Locales'], ['/mapa', 'Mapa'], ['/puno-3d', 'Modelos 3D']]} />
        <FooterCol title="Turiscovery" links={[['/sobre-nosotros', 'Sobre nosotros'], ['/contacto', 'Contacto'], ['/terminos', 'Términos y condiciones'], ['/privacidad', 'Política de privacidad']]} />
      </div>
      <div className="border-t border-white/10">
        <p className="container-page py-5 text-xs text-white/60">© {new Date().getFullYear()} Turiscovery · Puno, Perú</p>
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
