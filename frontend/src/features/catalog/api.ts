import { keepPreviousData, queryOptions, useQuery } from '@tanstack/react-query'
import { getData, type Paginated } from '@/shared/api/client'
import type { AnyContent, Favorite, Review } from '@/shared/api/types'
import type { ContentType } from '@/shared/config/content'

type Params = Record<string, unknown>

/**
 * Claves jerárquicas del catálogo. Invalidar `catalogKeys.type('experiencias')`
 * refresca su listado público, sus fichas y el listado propio del socio a la vez.
 */
export const catalogKeys = {
  all: ['catalog'] as const,
  type: (type: ContentType) => [...catalogKeys.all, type] as const,
  list: (type: ContentType, params: Params = {}) => [...catalogKeys.type(type), 'list', params] as const,
  item: (type: ContentType, id: string | number) => [...catalogKeys.type(type), 'item', String(id)] as const,
  /** Contenido propio del socio (o de todos, para el admin) */
  mine: (type: ContentType, params: Params = {}) => [...catalogKeys.type(type), 'mine', params] as const,
  favorites: () => [...catalogKeys.all, 'favorites'] as const,
  favoritesPage: (page: number) => [...catalogKeys.favorites(), 'page', page] as const,
  favoriteCheck: (modelClass: string, id: number) => [...catalogKeys.favorites(), 'check', modelClass, id] as const,
  reviews: (modelClass: string, id: number) => [...catalogKeys.all, 'reviews', modelClass, id] as const,
}

export const catalogQueries = {
  list: <T = AnyContent>(type: ContentType, params: Params = {}) =>
    queryOptions({
      queryKey: catalogKeys.list(type, params),
      queryFn: () => getData<Paginated<T>>(`/${type}`, params),
      placeholderData: keepPreviousData,
    }),
  item: <T = AnyContent>(type: ContentType, id?: string | number) =>
    queryOptions({
      queryKey: catalogKeys.item(type, id ?? ''),
      queryFn: () => getData<T>(`/${type}/${id}`),
      enabled: id !== undefined && id !== '',
    }),
  mine: <T = AnyContent>(type: ContentType, params: Params = {}) =>
    queryOptions({
      queryKey: catalogKeys.mine(type, params),
      queryFn: () => getData<Paginated<T>>(`/my/${type}`, params),
      placeholderData: keepPreviousData,
    }),
  favorites: (page = 1) =>
    queryOptions({
      queryKey: catalogKeys.favoritesPage(page),
      queryFn: () => getData<Paginated<Favorite>>('/favorites', { page }),
    }),
  reviews: (modelClass: string, id: number) =>
    queryOptions({
      queryKey: catalogKeys.reviews(modelClass, id),
      queryFn: () => getData<Paginated<Review>>('/reviews', { reviewable_type: modelClass, reviewable_id: id }),
    }),
}

export function useContentList<T = AnyContent>(type: ContentType, params: Params = {}, enabled = true) {
  return useQuery({ ...catalogQueries.list<T>(type, params), enabled })
}

export function useContentItem<T = AnyContent>(type: ContentType, id?: string | number) {
  return useQuery(catalogQueries.item<T>(type, id))
}
