import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { Link } from 'react-router'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Tocapu } from '@/shared/textile/Weave'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { EmptyState, Pagination, Spinner, StatusBadge } from '@/shared/ui/Feedback'
import { useToast } from '@/shared/ui/Toast'
import { parseApiError } from '@/shared/api/client'
import { formatDate, formatPrice } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { bookingQueries, useCancelBooking } from '@/features/bookings/api'

export function MyBookingsPage() {
  useDocumentTitle('Reservas')
  const [page, setPage] = useState(1)
  const notify = useToast()
  const { data, isLoading } = useQuery(bookingQueries.mine(page))
  const cancelBooking = useCancelBooking()
  const cancel = {
    ...cancelBooking,
    mutate: (id: number) =>
      cancelBooking.mutate(id, {
        onSuccess: () => notify('Reserva cancelada'),
        onError: (e) => notify(parseApiError(e).message, 'error'),
      }),
  }

  return (
    <>
      <PageHeader title="Reservas" description="El socio confirma cada reserva y te contacta con los detalles." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="grid gap-3 lg:grid-cols-2">
          {data.data.map((b) => {
            const future = new Date(b.booking_date) > new Date()
            return (
              <article key={b.id} className="card flex flex-col gap-2 p-4">
                <div className="flex items-start justify-between gap-3">
                  <StatusBadge status={b.status} />
                  <span className="font-display t-h3">{Number(b.total_price) > 0 ? formatPrice(b.total_price) : ''}</span>
                </div>
                <Link to={`/experiencias/${b.experiencia_id}`} className="t-h3 hover:text-primary-dark">{b.experiencia?.title ?? 'Experiencia'}</Link>
                <p className="text-sm text-ink-soft first-letter:uppercase">
                  {formatDate(b.booking_date)} · {b.participants} persona{b.participants > 1 ? 's' : ''}
                </p>
                <div className="flex flex-wrap items-center justify-between gap-3 border-t-2 border-line pt-3 text-sm">
                  <span className="text-ink-soft">
                    {b.status === 'pending' && 'Esperando que el socio la confirme'}
                    {b.status === 'confirmed' && 'Confirmada · muéstrala al llegar'}
                    {b.status === 'cancelled' && 'Cancelada · sin costo'}
                    {b.status === 'completed' && (
                      <Link to={`/experiencias/${b.experiencia_id}#resenas`} className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Cuéntanos cómo te fue</Link>
                    )}
                  </span>
                  {['pending', 'confirmed'].includes(b.status) && future && (
                    <Button size="sm" variant="ghost" loading={cancel.isPending && cancel.variables === b.id} loadingText="Cancelando…" onClick={() => confirm('¿Cancelar esta reserva?') && cancel.mutate(b.id)}>
                      Cancelar reserva
                    </Button>
                  )}
                </div>
              </article>
            )
          })}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="No tienes reservas" icon={<Tocapu motif="olas" dye="anil" size={40} framed={false} />} action={<ButtonLink to="/experiencias">Ver experiencias</ButtonLink>}>
          Reserva un tour o taller desde la página de cualquier experiencia.
        </EmptyState>
      )}
    </>
  )
}
