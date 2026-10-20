import { clsx } from 'clsx'
import { AlertTriangle, Check, ChevronLeft, ChevronRight, CircleAlert, CircleCheck, Info, LoaderCircle, Lock, Mountain, SlidersHorizontal, Star, WifiOff } from 'lucide-react'
import type { ButtonHTMLAttributes, ReactNode } from 'react'
import { Tocapu, type Dye, type Motif } from '../textile/Weave'

/* ---------- Carga ---------- */

export function Spinner({ label = 'Cargando…' }: { label?: string }) {
  return (
    <div role="status" className="flex items-center justify-center gap-2 py-16 text-ink-soft">
      <LoaderCircle className="size-5 animate-spin" aria-hidden />
      <span>{label}</span>
    </div>
  )
}

/**
 * Skeleton de tarjeta: sin brillo animado. El motivo del tipo aparece estático;
 * si el usuario permite movimiento, la faja "se teje" una vez.
 */
export function SkeletonCard({ motif = 'olas', dye = 'anil' }: { motif?: Motif; dye?: Dye }) {
  return (
    <div className="overflow-hidden rounded-[var(--radius-card)] border-2 border-line bg-white" aria-hidden>
      <div className="flex aspect-[4/3] items-center justify-center bg-sky">
        <Tocapu motif={motif} dye={dye} size={40} framed={false} className="opacity-30" />
      </div>
      <div className="flex flex-col gap-2.5 p-4">
        <div className="h-4 w-3/4 rounded-full bg-line" />
        <div className="h-3 w-1/2 rounded-full bg-line/70" />
      </div>
    </div>
  )
}

export function CardGridSkeleton({ count = 6, motif, dye }: { count?: number; motif?: Motif; dye?: Dye }) {
  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" role="status" aria-label="Cargando">
      {Array.from({ length: count }, (_, i) => <SkeletonCard key={i} motif={motif} dye={dye} />)}
    </div>
  )
}

/* ---------- Estados obligatorios: vacío, error, sin conexión, sin permisos, éxito ---------- */

export type StateKind = 'empty' | 'error' | 'offline' | 'forbidden' | 'success'

const STATE_STYLE: Record<StateKind, { box: string; title: string; text: string; icon: ReactNode }> = {
  empty: { box: 'border-2 border-ink bg-white', title: 'text-ink', text: 'text-ink-soft', icon: null },
  error: { box: 'border-2 border-ink bg-white', title: 'text-ink', text: 'text-ink-soft', icon: <AlertTriangle className="size-8 text-cochinilla" aria-hidden /> },
  offline: { box: 'border-2 border-sun bg-sun', title: 'text-ink', text: 'text-ink', icon: <WifiOff className="size-8 text-ink" aria-hidden /> },
  forbidden: { box: 'border-2 border-ink bg-white', title: 'text-ink', text: 'text-ink-soft', icon: <Lock className="size-8 text-ink" aria-hidden /> },
  success: { box: 'border-2 border-ink bg-ink', title: 'text-white', text: 'text-white/85', icon: <Tocapu motif="estrella" dye="mint" size={48} framed={false} /> },
}

/** Panel de estado de una pantalla o sección, con título, explicación y una acción */
export function StatePanel({
  kind,
  title,
  children,
  action,
  icon,
  className,
}: {
  kind: StateKind
  title: string
  children?: ReactNode
  action?: ReactNode
  icon?: ReactNode
  className?: string
}) {
  const s = STATE_STYLE[kind]
  return (
    <div
      role={kind === 'error' ? 'alert' : 'status'}
      className={clsx('flex flex-col items-start gap-3 rounded-[var(--radius-card)] p-6 sm:p-8', s.box, className)}
    >
      {icon ?? s.icon}
      <h3 className={clsx('t-h3', s.title)}>{title}</h3>
      {children && <div className={clsx('prose-measure', s.text)}>{children}</div>}
      {action && <div className="mt-1">{action}</div>}
    </div>
  )
}

export function EmptyState({ title, children, action, icon }: { title: string; children?: ReactNode; action?: ReactNode; icon?: ReactNode }) {
  return (
    <StatePanel kind="empty" title={title} action={action} icon={icon}>
      {children}
    </StatePanel>
  )
}

export function ErrorState({ message, onRetry, title = 'No pudimos cargar esto' }: { message: string; onRetry?: () => void; title?: string }) {
  return (
    <StatePanel
      kind="error"
      title={title}
      action={
        onRetry && (
          <button onClick={onRetry} className="min-h-11 rounded-[var(--radius-control)] border-2 border-ink bg-white px-4 font-bold hover:bg-sky">
            Reintentar
          </button>
        )
      }
    >
      {message}
    </StatePanel>
  )
}

/* ---------- Alertas ---------- */

type AlertTone = 'info' | 'success' | 'warning' | 'error'

const ALERT_STYLE: Record<AlertTone, { box: string; icon: ReactNode }> = {
  info: { box: 'border-info bg-info/60', icon: <Info className="size-5 shrink-0" aria-hidden /> },
  success: { box: 'border-mint-dark bg-mint/25', icon: <CircleCheck className="size-5 shrink-0 text-mint-dark" aria-hidden /> },
  // Avisos de altura y de cuidado: el ícono de montaña los identifica
  warning: { box: 'border-sun-dark bg-sun/20', icon: <Mountain className="size-5 shrink-0" aria-hidden /> },
  error: { box: 'border-danger bg-danger/10', icon: <CircleAlert className="size-5 shrink-0 text-danger" aria-hidden /> },
}

export function Alert({ tone = 'info', icon, children }: { tone?: AlertTone; icon?: ReactNode; children: ReactNode }) {
  const s = ALERT_STYLE[tone]
  return (
    <div role={tone === 'error' ? 'alert' : 'status'} className={clsx('flex items-start gap-3 rounded-[var(--radius-control)] border-2 px-4 py-3 text-ink', s.box)}>
      <span className="mt-0.5">{icon ?? s.icon}</span>
      <div className="min-w-0 flex-1">{children}</div>
    </div>
  )
}

/* ---------- Insignias ---------- */

export function Badge({ children, className }: { children: ReactNode; className?: string }) {
  return (
    <span className={clsx('inline-flex items-center gap-1 rounded-[var(--radius-chip)] px-2.5 py-0.5 text-xs font-bold', className ?? 'border-2 border-ink bg-white text-ink')}>
      {children}
    </span>
  )
}

export type StatusKey =
  | 'pending'
  | 'confirmed'
  | 'cancelled'
  | 'completed'
  | 'available'
  | 'used'
  | 'expired'
  | 'maybe'
  | 'published'
  | 'hidden'
  | 'active'
  | 'inactive'

const STATUS: Record<StatusKey, { label: string; cls: string }> = {
  pending: { label: 'Pendiente', cls: 'border-2 border-sun-dark bg-sun/20 text-[#6b4f00]' },
  confirmed: { label: 'Confirmada', cls: 'border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]' },
  cancelled: { label: 'Cancelada', cls: 'border-2 border-cochinilla bg-cochinilla/10 text-cochinilla' },
  completed: { label: 'Completada', cls: 'border-2 border-ink bg-sky text-ink' },
  available: { label: 'Disponible', cls: 'border-2 border-mint bg-mint text-ink' },
  used: { label: 'Usado', cls: 'border-2 border-line bg-line text-ink' },
  expired: { label: 'Vencido', cls: 'border-2 border-dashed border-cochinilla bg-transparent text-cochinilla' },
  maybe: { label: 'Quizás', cls: 'border-2 border-sun-dark bg-sun/20 text-[#6b4f00]' },
  published: { label: 'Publicado', cls: 'border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]' },
  hidden: { label: 'Oculto', cls: 'border-2 border-dashed border-ink-soft bg-transparent text-ink-soft' },
  active: { label: 'Activa', cls: 'border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]' },
  inactive: { label: 'Desactivada', cls: 'border-2 border-cochinilla bg-cochinilla/10 text-cochinilla' },
}

/** Estado de reservas, cupones y asistencias, con texto (no solo color) */
export function StatusBadge({ status, label }: { status: StatusKey; label?: string }) {
  return <Badge className={STATUS[status].cls}>{label ?? STATUS[status].label}</Badge>
}

const DIFFICULTY_LEVEL = { easy: 1, medium: 2, hard: 3 } as const
const DIFFICULTY_TEXT = { easy: 'Fácil', medium: 'Media', hard: 'Difícil' } as const
const DIFFICULTY_BAR = { easy: 'bg-chilca', medium: 'bg-qolle', hard: 'bg-cochinilla' } as const

/** Dificultad con barras (1 a 3), además del texto: no depende solo del color */
export function DifficultyBadge({ level }: { level: keyof typeof DIFFICULTY_LEVEL }) {
  const n = DIFFICULTY_LEVEL[level]
  return (
    <Badge className="border-2 border-ink bg-white text-ink">
      <span className="flex gap-0.5" aria-hidden>
        {[1, 2, 3].map((i) => (
          <span key={i} className={clsx('h-3 w-1.5 rounded-[1px]', i <= n ? DIFFICULTY_BAR[level] : 'bg-line')} />
        ))}
      </span>
      {DIFFICULTY_TEXT[level]}
    </Badge>
  )
}

/* ---------- Chips de filtro ---------- */

interface ChipProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  selected?: boolean
  /** Muestra el ícono de filtros y la cantidad activa: "Más filtros · 2" */
  count?: number
  icon?: ReactNode
}

export function FilterChip({ selected = false, count, icon, className, children, ...rest }: ChipProps) {
  return (
    <button
      type="button"
      aria-pressed={selected}
      className={clsx(
        'inline-flex min-h-11 items-center gap-1.5 rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors',
        selected ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky',
        className,
      )}
      {...rest}
    >
      {selected ? <Check className="size-4" aria-hidden /> : count !== undefined ? <SlidersHorizontal className="size-4" aria-hidden /> : icon}
      {children}
      {count !== undefined && count > 0 && <span>· {count}</span>}
    </button>
  )
}

/* ---------- Paginación ---------- */

export function Pagination({ page, lastPage, onChange }: { page: number; lastPage: number; onChange: (p: number) => void }) {
  if (lastPage <= 1) return null
  const btn = 'inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white hover:bg-sky disabled:border-line disabled:text-ink-soft disabled:hover:bg-white'
  return (
    <nav aria-label="Paginación" className="mt-10 flex items-center justify-center gap-3">
      <button className={btn} onClick={() => onChange(page - 1)} disabled={page <= 1} aria-label="Página anterior">
        <ChevronLeft className="size-4" />
      </button>
      <span className="text-sm text-ink-soft">
        Página <strong className="text-ink">{page}</strong> de {lastPage}
      </span>
      <button className={btn} onClick={() => onChange(page + 1)} disabled={page >= lastPage} aria-label="Página siguiente">
        <ChevronRight className="size-4" />
      </button>
    </nav>
  )
}

/* ---------- Estrellas ---------- */

export function Stars({ value, size = 'sm' }: { value: number; size?: 'sm' | 'md' | 'lg' }) {
  const s = { sm: 'size-4', md: 'size-5', lg: 'size-7' }[size]
  return (
    <span className="inline-flex items-center gap-0.5" role="img" aria-label={`${value.toFixed(1)} de 5 estrellas`}>
      {[1, 2, 3, 4, 5].map((i) => (
        <Star key={i} className={clsx(s, i <= Math.round(value) ? 'fill-sun-dark text-sun-dark' : 'text-ink')} aria-hidden />
      ))}
    </span>
  )
}

export function StarInput({ value, onChange }: { value: number; onChange: (v: number) => void }) {
  return (
    <div role="radiogroup" aria-label="Calificación" className="flex gap-0.5">
      {[1, 2, 3, 4, 5].map((i) => (
        <button
          key={i}
          type="button"
          role="radio"
          aria-checked={value === i}
          aria-label={`${i} estrella${i > 1 ? 's' : ''}`}
          onClick={() => onChange(i)}
          className="inline-flex size-11 items-center justify-center rounded-[var(--radius-chip)]"
        >
          <Star className={clsx('size-7 transition-colors', i <= value ? 'fill-sun-dark text-sun-dark' : 'text-ink hover:text-sun-dark')} />
        </button>
      ))}
    </div>
  )
}
