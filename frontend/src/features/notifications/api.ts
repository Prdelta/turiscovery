import { queryOptions, useMutation, useQueryClient } from '@tanstack/react-query'
import { api, getData } from '@/shared/api/client'

/** Lo que guarda el back en cada aviso (ver BookingNotification::toArray) */
export interface NotificationData {
  kind: 'booking'
  event: 'requested' | 'cancelled_by_tourist' | 'confirmed' | 'cancelled' | 'completed' | 'reminder'
  booking_id: number
  experiencia_id: number
  title: string
  message: string
  /** Ruta del frontend a la que lleva el aviso */
  url: string
}

export interface AppNotification {
  id: string
  data: NotificationData
  read_at: string | null
  created_at: string
}

export const notificationKeys = {
  all: ['notifications'] as const,
}

export const notificationQueries = {
  /** Se consulta cada minuto mientras la app está abierta: así el socio ve las reservas nuevas sin recargar */
  list: () =>
    queryOptions({
      queryKey: notificationKeys.all,
      queryFn: () => getData<{ unread: number; items: AppNotification[] }>('/notifications'),
      refetchInterval: 60_000,
      staleTime: 30_000,
    }),
}

export function useMarkRead() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: (id: string) => api.post(`/notifications/${id}/read`),
    onSuccess: () => qc.invalidateQueries({ queryKey: notificationKeys.all }),
  })
}

export function useMarkAllRead() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: () => api.post('/notifications/read-all'),
    onSuccess: () => qc.invalidateQueries({ queryKey: notificationKeys.all }),
  })
}
