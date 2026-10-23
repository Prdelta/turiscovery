import { queryOptions, useQuery } from '@tanstack/react-query'
import { getData } from '@/shared/api/client'
import type { Danza, GalleryPhoto, LiveState, Performance, ProgramItem } from '@/shared/api/types'

export interface ControlData {
  item: ProgramItem
  performances: Performance[]
  state: LiveState
}

/**
 * Candelaria: danzas, galería, programa y desfile en vivo.
 * Las vistas de administración cuelgan de la misma raíz, así un cambio del admin
 * refresca también lo que ve el público.
 */
export const candelariaKeys = {
  all: ['candelaria'] as const,
  danzas: () => [...candelariaKeys.all, 'danzas'] as const,
  danza: (id: string | number) => [...candelariaKeys.danzas(), String(id)] as const,
  gallery: () => [...candelariaKeys.all, 'gallery'] as const,
  program: () => [...candelariaKeys.all, 'program'] as const,
  live: () => [...candelariaKeys.all, 'live'] as const,
  admin: {
    danzas: () => [...candelariaKeys.danzas(), 'admin'] as const,
    gallery: () => [...candelariaKeys.gallery(), 'admin'] as const,
    program: () => [...candelariaKeys.program(), 'admin'] as const,
    performances: (programId: string | number) => [...candelariaKeys.program(), 'admin', 'performances', String(programId)] as const,
  },
}

export const candelariaQueries = {
  danzas: () => queryOptions({ queryKey: candelariaKeys.danzas(), queryFn: () => getData<Danza[]>('/candelaria-danzas'), staleTime: 300_000 }),
  danza: (id?: string) => queryOptions({ queryKey: candelariaKeys.danza(id ?? ''), queryFn: () => getData<Danza>(`/candelaria-danzas/${id}`), enabled: !!id }),
  gallery: () =>
    queryOptions({ queryKey: candelariaKeys.gallery(), queryFn: () => getData<GalleryPhoto[]>('/candelaria-gallery'), staleTime: 300_000 }),
  program: () =>
    queryOptions({ queryKey: candelariaKeys.program(), queryFn: () => getData<{ year: number; items: ProgramItem[] }>('/candelaria-program') }),
  live: () => queryOptions({ queryKey: candelariaKeys.live(), queryFn: () => getData<LiveState>('/candelaria-live'), staleTime: 60_000 }),
  admin: {
    danzas: () => queryOptions({ queryKey: candelariaKeys.admin.danzas(), queryFn: () => getData<Danza[]>('/admin/candelaria-danzas') }),
    gallery: () => queryOptions({ queryKey: candelariaKeys.admin.gallery(), queryFn: () => getData<GalleryPhoto[]>('/admin/candelaria-gallery') }),
    program: () => queryOptions({ queryKey: candelariaKeys.admin.program(), queryFn: () => getData<ProgramItem[]>('/admin/candelaria-program') }),
    performances: (programId?: string) =>
      queryOptions({
        queryKey: candelariaKeys.admin.performances(programId ?? ''),
        queryFn: () => getData<ControlData>(`/admin/candelaria-program/${programId}/performances`),
        enabled: !!programId,
      }),
  },
}

export function useDanzas() {
  return useQuery(candelariaQueries.danzas())
}

export function useGallery() {
  return useQuery(candelariaQueries.gallery())
}
