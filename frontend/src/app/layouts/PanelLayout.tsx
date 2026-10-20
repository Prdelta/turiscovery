import { clsx } from 'clsx'
import { LogOut, UserCog } from 'lucide-react'
import { NavLink, Outlet, useNavigate } from 'react-router'
import { PANELS, panelNav } from '@/app/features'
import { useAuth } from '@/shared/auth/AuthContext'
import type { NavItem, PanelSection } from '@/shared/routing/feature'
import { Tocapu } from '@/shared/textile/Weave'
import { Avatar } from '@/shared/ui/Identity'

type MenuItem = NavItem & { to: string }

function ItemIcon({ item, size = 20 }: { item: MenuItem; size?: number }) {
  if (item.motif) {
    return (
      <span className="inline-flex shrink-0 items-center justify-center rounded-[6px] bg-lana p-0.5">
        <Tocapu motif={item.motif[0]} dye={item.motif[1]} size={size - 4} framed={false} />
      </span>
    )
  }
  if (!item.icon) return null
  const Icon = item.icon
  return <Icon className="shrink-0" style={{ width: size, height: size }} aria-hidden />
}

/** Pestañas en chips: Mi cuenta en todos los tamaños y los paneles en móvil */
function ChipTabs({ items, className }: { items: MenuItem[]; className?: string }) {
  return (
    <nav aria-label="Secciones" className={clsx('-mx-4 overflow-x-auto px-4 pb-1', className)}>
      <ul className="flex w-max gap-2">
        {items.map((item) => (
          <li key={item.to}>
            <NavLink
              to={item.to}
              end={!item.path}
              className={({ isActive }) =>
                clsx(
                  'flex min-h-11 items-center gap-2 whitespace-nowrap rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors',
                  isActive ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky',
                )
              }
            >
              {item.label}
              {item.Badge && <item.Badge />}
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  )
}

/**
 * Layout de los paneles. El menú no se escribe aquí: lo aportan las features
 * (ver app/features.ts), cada una con sus entradas, su orden y su contador.
 */
export function PanelLayout({ section }: { section: PanelSection }) {
  const { user, logout, hasRole } = useAuth()
  const navigate = useNavigate()

  // Mi cuenta: encabezado y pestañas en chips, sin panel lateral
  if (section === 'account') {
    return (
      <div className="container-page py-8 lg:py-12">
        <div className="mb-6 flex items-center gap-3">
          {user && <Avatar name={user.name} src={user.avatar} />}
          <p className="t-h2">{PANELS.account.title}</p>
        </div>
        <ChipTabs items={panelNav('account')} className="mb-8" />
        <Outlet />
      </div>
    )
  }

  // Los admins también gestionan contenido como socios
  const sections: PanelSection[] = [section]
  if (section === 'admin') sections.push('partner')
  if (section === 'partner' && hasRole('admin')) sections.push('admin')
  const groups = sections.map((s) => ({ title: PANELS[s].title, items: panelNav(s) }))

  return (
    <div className="container-page grid gap-8 py-8 lg:grid-cols-[264px_1fr] lg:py-12">
      {/* Móvil: chips con las secciones del panel */}
      <div className="lg:hidden">
        <ChipTabs items={groups.flatMap((g) => g.items)} />
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
                      end={!item.path}
                      className={({ isActive }) =>
                        clsx(
                          'flex min-h-11 items-center gap-3 rounded-[var(--radius-control)] px-3 text-[0.95rem] font-bold transition-colors',
                          isActive ? 'bg-lana text-ink' : 'text-white hover:bg-white/10',
                        )
                      }
                    >
                      <ItemIcon item={item} />
                      <span className="flex-1">{item.label}</span>
                      {item.Badge && <item.Badge />}
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
