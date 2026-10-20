import { clsx } from 'clsx'
import type { ReactNode } from 'react'
import { Link } from 'react-router'
import { Tocapu, type Dye, type Motif } from '@/shared/textile/Weave'
import { FilterChip } from '@/shared/ui/Feedback'

/** Enlace de texto de la guía Telar: subrayado grueso, legible sobre lana y blanco */
export const TEXT_LINK = 'font-bold text-primary-dark underline decoration-2 underline-offset-4 hover:text-primary'

/** Bloque de panel: tarjeta con título y acción opcional a la derecha */
export function PanelCard({
  title,
  action,
  children,
  className,
  bodyClassName,
}: {
  title?: ReactNode
  action?: ReactNode
  children: ReactNode
  className?: string
  bodyClassName?: string
}) {
  return (
    <section className={clsx('card flex flex-col', className)}>
      {title && (
        <div className="flex flex-wrap items-center justify-between gap-3 border-b-2 border-ink px-5 py-4">
          <h2 className="t-h3">{title}</h2>
          {action}
        </div>
      )}
      <div className={clsx('p-5', bodyClassName)}>{children}</div>
    </section>
  )
}

/** Cifra del resumen con el motivo de su tipo */
export function StatTile({
  to,
  value,
  label,
  note,
  motif,
  dye,
}: {
  to?: string
  value: ReactNode
  label: string
  note?: ReactNode
  motif: Motif
  dye: Dye
}) {
  const body = (
    <>
      <Tocapu motif={motif} dye={dye} size={28} />
      <span className="font-display text-4xl font-extrabold leading-none">{value}</span>
      <span className="text-sm font-bold text-ink-soft">{label}</span>
      {note && <span className="text-xs text-ink-soft">{note}</span>}
    </>
  )
  const cls = 'card flex flex-col gap-2 p-5'
  return to ? (
    <Link to={to} className={clsx(cls, 'transition-colors hover:bg-sky')}>
      {body}
    </Link>
  ) : (
    <div className={cls}>{body}</div>
  )
}

const ICON_ACTION =
  'inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 transition-colors disabled:border-line disabled:text-ink-soft'

/** Acción de ícono de 44 px con borde (editar, ocultar, eliminar) */
export function IconAction({
  label,
  onClick,
  to,
  danger,
  disabled,
  children,
}: {
  label: string
  onClick?: () => void
  to?: string
  danger?: boolean
  disabled?: boolean
  children: ReactNode
}) {
  const cls = clsx(ICON_ACTION, danger ? 'border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white' : 'border-ink bg-white text-ink hover:bg-sky')
  if (to) {
    return (
      <Link to={to} aria-label={label} title={label} className={cls}>
        {children}
      </Link>
    )
  }
  return (
    <button type="button" onClick={onClick} aria-label={label} title={label} disabled={disabled} className={cls}>
      {children}
    </button>
  )
}

/** Grupo de chips de filtro de una sola elección (estado de reservas, roles, etc.) */
export function ChipGroup<T extends string>({
  label,
  options,
  value,
  onChange,
  className,
}: {
  label: string
  options: [T, string][]
  value: T
  onChange: (v: T) => void
  className?: string
}) {
  return (
    <div role="group" aria-label={label} className={clsx('-mx-4 overflow-x-auto px-4 pb-1', className)}>
      <div className="flex w-max gap-2">
        {options.map(([v, l]) => (
          <FilterChip key={v || 'all'} selected={value === v} onClick={() => onChange(v)}>
            {l}
          </FilterChip>
        ))}
      </div>
    </div>
  )
}
