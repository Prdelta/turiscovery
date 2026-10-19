import { clsx } from 'clsx'
import { NavLink } from 'react-router'
import { CONTENT, type ContentType } from '../../lib/content'
import { DYES, StitchRow, Tocapu, motifPath } from './Weave'

/**
 * Pestaña de identificación: cada tarjeta lleva el motivo y el tinte de su tipo.
 * Texto blanco sobre el tinte (todos pasan 4.5:1, salvo q'olle, que no se usa aquí).
 */
export function TypeTag({ type, detail, className }: { type: ContentType; detail?: string; className?: string }) {
  const meta = CONTENT[type]
  return (
    <span
      className={clsx('inline-flex min-h-8 items-center gap-1.5 rounded-[var(--radius-chip)] px-2.5 py-1 text-sm font-bold text-white', className)}
      style={{ background: DYES[meta.dye] }}
    >
      <svg width={16} height={16} viewBox="0 0 8 8" shapeRendering="crispEdges" aria-hidden>
        <path d={motifPath(meta.motif)} fill="#ffffff" />
      </svg>
      {meta.singular}
      {detail ? ` · ${detail}` : ''}
    </span>
  )
}

const NAV_TYPES: { type: ContentType; to: string; label: string }[] = [
  { type: 'experiencias', to: '/experiencias', label: 'Experiencias' },
  { type: 'eventos', to: '/eventos', label: 'Eventos' },
  { type: 'promociones', to: '/promociones', label: 'Promociones' },
  { type: 'locales', to: '/locales', label: 'Locales' },
]

/**
 * Faja de navegación: banda de tinta con los tipos de contenido.
 * El tipo activo lleva fondo lana.
 */
export function CategoryNavBand({ className }: { className?: string }) {
  return (
    <nav aria-label="Tipos de contenido" className={clsx('bg-ink', className)}>
      <StitchRow dye="qolle" className="opacity-90" />
      <ul className="mx-auto flex max-w-3xl justify-between gap-1 px-2 py-2">
        {NAV_TYPES.map(({ type, to, label }) => {
          const meta = CONTENT[type]
          return (
            <li key={type} className="flex-1">
              <NavLink
                to={to}
                className={({ isActive }) =>
                  clsx(
                    'flex min-h-11 flex-col items-center gap-1 rounded-[var(--radius-control)] px-1 py-2 text-center text-xs font-bold transition-colors sm:text-sm',
                    isActive ? 'bg-lana text-ink' : 'text-white hover:bg-white/10',
                  )
                }
              >
                <span className="inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] bg-lana">
                  <Tocapu motif={meta.motif} dye={meta.dye} size={28} framed={false} />
                </span>
                {label}
              </NavLink>
            </li>
          )
        })}
      </ul>
      <StitchRow dye="qolle" className="opacity-90" />
    </nav>
  )
}
