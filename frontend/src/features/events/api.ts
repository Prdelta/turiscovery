import { queryOptions } from '@tanstack/react-query'
import { api } from '@/shared/api/client'
import type { EventAttendance } from '@/shared/api/types'

/** Asistencia a eventos: cuántos van y si el usuario confirmó */
export const eventKeys = {
  all: ['events'] as const,
  attendance: (eventoId: number) => [...eventKeys.all, 'attendance', eventoId] as const,
}

export const eventQueries = {
  attendanceCount: (eventoId: number) =>
    queryOptions({
      queryKey: [...eventKeys.attendance(eventoId), 'count'] as const,
      queryFn: async () => (await api.get<{ data: { confirmed: number; maybe: number } }>(`/event-attendances/count/${eventoId}`)).data.data,
    }),
  myAttendance: (eventoId: number) =>
    queryOptions({
      queryKey: [...eventKeys.attendance(eventoId), 'mine'] as const,
      queryFn: async () => (await api.get<{ attending: boolean; data: EventAttendance | null }>(`/event-attendances/check/${eventoId}`)).data,
    }),
}
