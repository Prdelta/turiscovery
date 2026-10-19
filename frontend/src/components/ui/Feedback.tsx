import { clsx } from 'clsx'
import { AlertTriangle, ChevronLeft, ChevronRight, LoaderCircle, Star } from 'lucide-react'
import type { ReactNode } from 'react'

export function Spinner({ label = 'Cargando…' }: { label?: string }) {
  return (
    <div role="status" className="flex items-center justify-center gap-2 py-16 text-ink-soft">
      <LoaderCircle className="size-5 animate-spin" aria-hidden />
      <span>{label}</span>
    </div>
  )
}

export function CardGridSkeleton({ count = 6 }: { count?: number }) {
  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" aria-hidden>
      {Array.from({ length: count }, (_, i) => (
        <div key={i} className="flex flex-col gap-3">
          <div className="skeleton aspect-[4/3]" />
          <div className="skeleton h-5 w-3/4" />
          <div className="skeleton h-4 w-1/2" />
        </div>
      ))}
    </div>
  )
}

export function EmptyState({ title, children, action, icon }: { title: string; children?: ReactNode; action?: ReactNode; icon?: ReactNode }) {
  return (
    <div className="flex flex-col items-center gap-3 rounded-[var(--radius-card)] border border-dashed border-line bg-white px-6 py-14 text-center">
      {icon && <div className="text-ink-soft">{icon}</div>}
      <h3 className="text-xl font-bold">{title}</h3>
      {children && <p className="max-w-md text-ink-soft">{children}</p>}
      {action && <div className="mt-2">{action}</div>}
    </div>
  )
}

export function ErrorState({ message, onRetry }: { message: string; onRetry?: () => void }) {
  return (
    <div role="alert" className="flex flex-col items-center gap-3 rounded-[var(--radius-card)] border border-danger/30 bg-white px-6 py-12 text-center">
      <AlertTriangle className="size-7 text-danger" aria-hidden />
      <p className="max-w-md text-ink">{message}</p>
      {onRetry && (
        <button onClick={onRetry} className="text-sm font-semibold text-primary underline-offset-4 hover:underline">
          Intentar de nuevo
        </button>
      )}
    </div>
  )
}

export function Alert({ tone = 'info', children }: { tone?: 'info' | 'success' | 'error'; children: ReactNode }) {
  return (
    <div
      role={tone === 'error' ? 'alert' : 'status'}
      className={clsx(
        'rounded-xl border px-4 py-3 text-sm',
        tone === 'error' && 'border-danger/30 bg-danger/5 text-danger',
        tone === 'success' && 'border-mint-dark/30 bg-mint/15 text-ink',
        tone === 'info' && 'border-info bg-info/30 text-ink',
      )}
    >
      {children}
    </div>
  )
}

export function Badge({ children, className }: { children: ReactNode; className?: string }) {
  return (
    <span className={clsx('inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold', className ?? 'bg-ink/8 text-ink')}>
      {children}
    </span>
  )
}

export function Pagination({ page, lastPage, onChange }: { page: number; lastPage: number; onChange: (p: number) => void }) {
  if (lastPage <= 1) return null
  return (
    <nav aria-label="Paginación" className="mt-10 flex items-center justify-center gap-3">
      <button
        className="inline-flex size-10 items-center justify-center rounded-full border border-line bg-white disabled:opacity-40"
        onClick={() => onChange(page - 1)}
        disabled={page <= 1}
        aria-label="Página anterior"
      >
        <ChevronLeft className="size-4" />
      </button>
      <span className="text-sm text-ink-soft">
        Página <strong className="text-ink">{page}</strong> de {lastPage}
      </span>
      <button
        className="inline-flex size-10 items-center justify-center rounded-full border border-line bg-white disabled:opacity-40"
        onClick={() => onChange(page + 1)}
        disabled={page >= lastPage}
        aria-label="Página siguiente"
      >
        <ChevronRight className="size-4" />
      </button>
    </nav>
  )
}

export function Stars({ value, size = 'sm' }: { value: number; size?: 'sm' | 'md' }) {
  const s = size === 'sm' ? 'size-4' : 'size-5'
  return (
    <span className="inline-flex items-center gap-0.5" aria-label={`${value.toFixed(1)} de 5 estrellas`}>
      {[1, 2, 3, 4, 5].map((i) => (
        <Star key={i} className={clsx(s, i <= Math.round(value) ? 'fill-sun text-sun' : 'text-line')} aria-hidden />
      ))}
    </span>
  )
}

export function StarInput({ value, onChange }: { value: number; onChange: (v: number) => void }) {
  return (
    <div role="radiogroup" aria-label="Calificación" className="flex gap-1">
      {[1, 2, 3, 4, 5].map((i) => (
        <button
          key={i}
          type="button"
          role="radio"
          aria-checked={value === i}
          aria-label={`${i} estrella${i > 1 ? 's' : ''}`}
          onClick={() => onChange(i)}
          className="rounded-md p-0.5"
        >
          <Star className={clsx('size-7 transition-colors', i <= value ? 'fill-sun text-sun' : 'text-line hover:text-sun')} />
        </button>
      ))}
    </div>
  )
}
