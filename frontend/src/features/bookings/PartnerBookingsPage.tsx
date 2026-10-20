import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button } from '@/shared/ui/Button'
import { Tocapu } from '@/shared/textile/Weave'
import { CardGridSkeleton, EmptyState, Pagination, StatusBadge } from '@/shared/ui/Feedback'
import { ChipGroup, TEXT_LINK } from '@/shared/ui/Panel'
import { useToast } from '@/shared/ui/Toast'
import { parseApiError } from '@/shared/api/client'
import { formatDate, formatPrice } from '@/shared/lib/format'
import { useDocumentTitle, useNow } from '@/shared/lib/hooks'
import type { Booking } from '@/shared/api/types'
import { bookingQueries, useUpdatePartnerBooking } from '@/features/bookings/api'

export function PartnerBookingsPage() {
  useDocumentTitle('Reservas recibidas')
  const notify = useToast()
  const [status, setStatus] = useState('pending')
  const [page, setPage] = useState(1)
  const { data, isLoading } = useQuery(bookingQueries.partner(status, page))
  const updateBooking = useUpdatePartnerBooking()
  const update = {
    ...updateBooking,
    mutate: (vars: { id: number; status: Booking['status'] }) =>
      updateBooking.mutate(vars, {
        onSuccess: () => notify('Reserva actualizada'),
        onError: (e) => notify(parseApiError(e).message, 'error'),
      }),
  }
  // Fecha local en formato YYYY-MM-DD, comparable con booking_date
  const todayKey = useNow().toLocaleDateString('en-CA')
  const busy = (id: number, s: string) => update.isPending && update.variables?.id === id && update.variables.status === s

  return (
    <>
      <PageHeader title="Reservas recibidas" description="Confirma o rechaza las reservas de tus experiencias. El viajero ve el estado en su cuenta." />
      <ChipGroup
        label="Estado"
        className="mb-6"
        value={status}
        onChange={(v) => { setStatus(v); setPage(1) }}
        options={[['pending', 'Pendientes'], ['confirmed', 'Confirmadas'], ['completed', 'Completadas'], ['cancelled', 'Canceladas'], ['', 'Todas']]}
      />
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : data?.data.length ? (
        <>
          <ul className="flex flex-col gap-3">
            {data.data.map((b) => (
              <li key={b.id} className="card flex flex-wrap items-center gap-4 p-5">
                <div className="min-w-0 flex-1">
                  <div className="mb-1 flex flex-wrap items-center gap-2">
                    <StatusBadge status={b.status} />
                    <p className="font-bold">{b.experiencia?.title}</p>
                  </div>
                  <p className="text-sm text-ink-soft">
                    {formatDate(b.booking_date)} · {b.participants} persona{b.participants > 1 ? 's' : ''}
                    {Number(b.total_price) > 0 && ` · ${formatPrice(b.total_price)}`}
                  </p>
                  <p className="text-sm">
                    {b.user?.name}
                    {(b.contact_phone || b.user?.phone) && <> · <a href={`tel:${b.contact_phone || b.user?.phone}`} className={TEXT_LINK}>{b.contact_phone || b.user?.phone}</a></>}
                    {(b.contact_email || b.user?.email) && <> · <a href={`mailto:${b.contact_email || b.user?.email}`} className={TEXT_LINK}>{b.contact_email || b.user?.email}</a></>}
                  </p>
                  {b.notes && <p className="mt-1 text-sm text-ink-soft">“{b.notes}”</p>}
                </div>
                <div className="flex flex-wrap gap-2">
                  {b.status === 'pending' && (
                    <>
                      <Button size="sm" loading={busy(b.id, 'confirmed')} loadingText="Confirmando…" onClick={() => update.mutate({ id: b.id, status: 'confirmed' })}>
                        Confirmar
                      </Button>
                      <Button
                        size="sm"
                        variant="secondary"
                        loading={busy(b.id, 'cancelled')}
                        loadingText="Rechazando…"
                        onClick={() => confirm('¿Rechazar esta reserva?') && update.mutate({ id: b.id, status: 'cancelled' })}
                      >
                        Rechazar
                      </Button>
                    </>
                  )}
                  {/* Una confirmada se completa desde el día de la salida; antes solo se puede cancelar */}
                  {b.status === 'confirmed' && b.booking_date.slice(0, 10) <= todayKey && (
                    <Button size="sm" variant="secondary" loading={busy(b.id, 'completed')} loadingText="Guardando…" onClick={() => update.mutate({ id: b.id, status: 'completed' })}>
                      Marcar completada
                    </Button>
                  )}
                  {b.status === 'confirmed' && b.booking_date.slice(0, 10) > todayKey && (
                    <Button
                      size="sm"
                      variant="ghost"
                      loading={busy(b.id, 'cancelled')}
                      loadingText="Cancelando…"
                      onClick={() => confirm('¿Cancelar esta reserva confirmada? Le avisaremos al viajero.') && update.mutate({ id: b.id, status: 'cancelled' })}
                    >
                      Cancelar
                    </Button>
                  )}
                </div>
              </li>
            ))}
          </ul>
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState title="No hay reservas en este estado" icon={<Tocapu motif="olas" dye="anil" size={40} framed={false} />}>
          Cuando un viajero reserve una de tus experiencias, aparecerá aquí para que la confirmes.
        </EmptyState>
      )}
    </>
  )
}
