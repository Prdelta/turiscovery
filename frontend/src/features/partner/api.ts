import { queryOptions, useQueryClient } from '@tanstack/react-query'
import { getData } from '@/shared/api/client'
import type { AnyContent } from '@/shared/api/types'
import type { ContentType } from '@/shared/config/content'
import { catalogKeys } from '@/features/catalog/api'

/** Resumen del panel de socio: cifras y lo publicado hace poco */
export const partnerKeys = {
  all: ['partner'] as const,
  stats: () => [...partnerKeys.all, 'stats'] as const,
  recent: () => [...partnerKeys.all, 'recent'] as const,
}

export const partnerQueries = {
  stats: () => queryOptions({ queryKey: partnerKeys.stats(), queryFn: () => getData<Record<string, number>>('/stats') }),
  recent: (limit = 4) =>
    queryOptions({
      queryKey: partnerKeys.recent(),
      queryFn: () => getData<(AnyContent & { contentType: ContentType })[]>('/recent-content', { limit }),
    }),
}

/** Tras crear, editar, ocultar o borrar contenido: catálogo de ese tipo + resumen del panel */
export function useInvalidateContent() {
  const qc = useQueryClient()
  return (type: ContentType) => {
    void qc.invalidateQueries({ queryKey: catalogKeys.type(type) })
    void qc.invalidateQueries({ queryKey: partnerKeys.all })
  }
}
