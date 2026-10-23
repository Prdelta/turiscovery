import { queryOptions } from '@tanstack/react-query'
import { getData } from '@/shared/api/client'
import type { Anfitrion, HostLanguage } from '@/shared/api/types'

/** Anfitriones: directorio público, ficha y los que presenta cada socio */
export const hostKeys = {
  all: ['hosts'] as const,
  list: (language: HostLanguage | '' = '') => [...hostKeys.all, 'list', language] as const,
  item: (id: string | number) => [...hostKeys.all, 'item', String(id)] as const,
  mine: () => [...hostKeys.all, 'mine'] as const,
}

export const hostQueries = {
  list: (language: HostLanguage | '' = '') =>
    queryOptions({ queryKey: hostKeys.list(language), queryFn: () => getData<Anfitrion[]>('/anfitriones', { language: language || undefined }) }),
  item: (id?: string) => queryOptions({ queryKey: hostKeys.item(id ?? ''), queryFn: () => getData<Anfitrion>(`/anfitriones/${id}`), enabled: !!id }),
  mine: () => queryOptions({ queryKey: hostKeys.mine(), queryFn: () => getData<Anfitrion[]>('/my-anfitriones') }),
}
