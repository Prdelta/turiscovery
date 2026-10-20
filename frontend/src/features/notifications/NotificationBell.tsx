import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Bell, CheckCheck } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { useNavigate } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { formatShortDate } from '@/shared/lib/format'
import { Tocapu, type Dye, type Motif } from '@/shared/textile/Weave'
import { notificationQueries, useMarkAllRead, useMarkRead, type NotificationData } from './api'

/** Motivo y tinte de cada tipo de aviso (sigue los colores de estado de la guía Telar) */
const WEAVE: Record<NotificationData['event'], [Motif, Dye]> = {
  requested: ['olas', 'anil'],
  cancelled_by_tourist: ['olas', 'cochinilla'],
  confirmed: ['estrella', 'chilca'],
  cancelled: ['olas', 'cochinilla'],
  completed: ['sol', 'solOscuro'],
  reminder: ['sol', 'solOscuro'],
}

/**
 * Campana de avisos del encabezado: reservas nuevas para el socio,
 * confirmaciones y recordatorios para el viajero.
 */
export function NotificationBell() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const [open, setOpen] = useState(false)
  const box = useRef<HTMLDivElement>(null)
  const { data } = useQuery({ ...notificationQueries.list(), enabled: !!user })
  const markRead = useMarkRead()
  const markAll = useMarkAllRead()
  const unread = data?.unread ?? 0

  // Se cierra al tocar fuera o con Escape
  useEffect(() => {
    if (!open) return
    const onClick = (e: MouseEvent) => !box.current?.contains(e.target as Node) && setOpen(false)
    const onKey = (e: KeyboardEvent) => e.key === 'Escape' && setOpen(false)
    document.addEventListener('mousedown', onClick)
    document.addEventListener('keydown', onKey)
    return () => {
      document.removeEventListener('mousedown', onClick)
      document.removeEventListener('keydown', onKey)
    }
  }, [open])

  if (!user) return null

  return (
    <div ref={box} className="relative">
      <button
        onClick={() => setOpen((o) => !o)}
        aria-expanded={open}
        aria-haspopup="true"
        aria-label={unread ? `Avisos: ${unread} sin leer` : 'Avisos'}
        className="relative inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white hover:bg-sky lg:border-transparent lg:bg-transparent lg:hover:bg-white"
      >
        <Bell className="size-5" />
        {unread > 0 && (
          <span className="absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full border-2 border-ink bg-sun px-1 text-xs font-extrabold text-ink">
            {unread > 9 ? '9+' : unread}
          </span>
        )}
      </button>

      {open && (
        <div
          role="dialog"
          aria-label="Avisos"
          className="card fixed inset-x-4 top-20 z-[1200] flex max-h-[70vh] flex-col overflow-hidden sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:mt-2 sm:w-96"
        >
          <div className="flex items-center justify-between gap-3 border-b-2 border-ink px-4 py-3">
            <p className="t-h3">Avisos</p>
            {unread > 0 && (
              <button
                onClick={() => markAll.mutate()}
                className="inline-flex min-h-11 items-center gap-1.5 text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4"
              >
                <CheckCheck className="size-4" aria-hidden /> Marcar todo como leído
              </button>
            )}
          </div>
          {data?.items.length ? (
            <ul className="divide-y-2 divide-line overflow-y-auto">
              {data.items.map((n) => {
                const [motif, dye] = WEAVE[n.data.event] ?? ['olas', 'anil']
                return (
                  <li key={n.id}>
                    <button
                      onClick={() => {
                        if (!n.read_at) markRead.mutate(n.id)
                        setOpen(false)
                        navigate(n.data.url)
                      }}
                      className={clsx('flex w-full items-start gap-3 px-4 py-3 text-left hover:bg-sky', !n.read_at && 'bg-lana')}
                    >
                      <Tocapu motif={motif} dye={dye} size={24} className="mt-0.5" />
                      <span className="min-w-0 flex-1">
                        <span className={clsx('block text-sm', !n.read_at && 'font-bold')}>{n.data.message}</span>
                        <span className="text-xs text-ink-soft">{formatShortDate(n.created_at)}</span>
                      </span>
                      {!n.read_at && <span className="mt-1.5 size-2.5 shrink-0 rounded-full bg-cochinilla" aria-label="Sin leer" />}
                    </button>
                  </li>
                )
              })}
            </ul>
          ) : (
            <div className="flex flex-col items-center gap-2 px-4 py-8 text-center">
              <Tocapu motif="sol" dye="solOscuro" size={32} framed={false} />
              <p className="font-bold">Sin avisos por ahora</p>
              <p className="text-sm text-ink-soft">Aquí verás tus reservas nuevas, confirmaciones y recordatorios.</p>
            </div>
          )}
        </div>
      )}
    </div>
  )
}
