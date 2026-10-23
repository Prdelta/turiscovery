import { queryOptions, useMutation, useQueryClient } from '@tanstack/react-query'
import { api, getData, type Paginated } from '@/shared/api/client'
import type { Booking, DayAvailability, User } from '@/shared/api/types'
import { accountKeys } from '@/features/account/api'

export type PartnerBooking = Booking & { user?: User; contact_phone?: string; contact_email?: string }

/** Reservas: las del viajero, las que recibe el socio y los cupos por día */
export const bookingKeys = {
  all: ['bookings'] as const,
  mine: (page: number) => [...bookingKeys.all, 'mine', page] as const,
  partner: (status: string, page: number) => [...bookingKeys.all, 'partner', status, page] as const,
  availability: (experienciaId: string | number) => [...bookingKeys.all, 'availability', String(experienciaId)] as const,
}

export const bookingQueries = {
  mine: (page = 1) =>
    queryOptions({ queryKey: bookingKeys.mine(page), queryFn: () => getData<Paginated<Booking>>('/bookings', { page }) }),
  partner: (status = '', page = 1) =>
    queryOptions({
      queryKey: bookingKeys.partner(status, page),
      queryFn: () => getData<Paginated<PartnerBooking>>('/partner/bookings', { status: status || undefined, page }),
    }),
  availability: (experienciaId?: string | number, days = 14) =>
    queryOptions({
      queryKey: bookingKeys.availability(experienciaId ?? ''),
      queryFn: () => getData<DayAvailability[]>(`/experiencias/${experienciaId}/availability`, { days }),
      enabled: !!experienciaId,
      staleTime: 60_000,
    }),
}

/** Cualquier cambio en reservas mueve cupos, listados y los contadores de la cuenta */
function useInvalidateBookings() {
  const qc = useQueryClient()
  return () => {
    void qc.invalidateQueries({ queryKey: bookingKeys.all })
    void qc.invalidateQueries({ queryKey: accountKeys.profile() })
  }
}

export interface NewBooking {
  experiencia_id: number
  booking_date: string
  participants: number
  contact_phone?: string
  notes?: string
}

export function useCreateBooking() {
  const invalidate = useInvalidateBookings()
  // onSettled: si falla por cupos (422), también se refrescan los días para mostrar lo que queda
  return useMutation({ mutationFn: (body: NewBooking) => api.post('/bookings', body), onSettled: invalidate })
}

export function useCancelBooking() {
  const invalidate = useInvalidateBookings()
  return useMutation({ mutationFn: (id: number) => api.post(`/bookings/${id}/cancel`), onSuccess: invalidate })
}

export function useUpdatePartnerBooking() {
  const invalidate = useInvalidateBookings()
  return useMutation({
    mutationFn: ({ id, status }: { id: number; status: Booking['status'] }) => api.put(`/partner/bookings/${id}`, { status }),
    onSuccess: invalidate,
  })
}
