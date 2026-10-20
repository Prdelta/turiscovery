import { useQuery } from '@tanstack/react-query'
import { useAuth } from '@/shared/auth/AuthContext'
import { bookingQueries } from '@/features/bookings/api'

/** Contador del menú del panel: reservas que el socio aún no confirma */
export function PendingBookingsBadge() {
  const { hasRole } = useAuth()
  const { data } = useQuery({ ...bookingQueries.partner('pending', 1), enabled: hasRole('socio', 'admin'), staleTime: 60_000 })
  const total = data?.total ?? 0
  if (!total) return null
  return (
    <span className="rounded-full bg-sun px-2 text-xs font-extrabold text-ink" aria-label={`${total} pendientes`}>
      {total}
    </span>
  )
}
