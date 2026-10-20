import { queryOptions } from '@tanstack/react-query'
import { getData, type Paginated } from '@/shared/api/client'
import type { User } from '@/shared/api/types'

export interface Supervision {
  stats: Record<string, number>
  socios_mas_activos: User[]
  socios_inactivos: User[]
}
export interface WikiSummary {
  title: string
  extract: string
  description: string
  thumbnail: string | null
  url: string
}
export interface CommonsResult {
  title: string
  snippet: string
  page_url: string
}
export interface ImageResult {
  url: string
  title: string
  source: string
}

export interface UserFilters {
  search?: string
  role?: string
  status?: string
  page?: number
}

/** Administración: supervisión, usuarios y búsqueda de recursos */
export const adminKeys = {
  all: ['admin'] as const,
  supervision: () => [...adminKeys.all, 'supervision'] as const,
  users: () => [...adminKeys.all, 'users'] as const,
  userList: (filters: UserFilters) => [...adminKeys.users(), 'list', filters] as const,
  user: (id: string | number) => [...adminKeys.users(), String(id)] as const,
  resources: (kind: 'wiki' | 'images', query: string) => [...adminKeys.all, 'resources', kind, query] as const,
}

export const adminQueries = {
  supervision: () => queryOptions({ queryKey: adminKeys.supervision(), queryFn: () => getData<Supervision>('/admin/supervision') }),
  users: (filters: UserFilters) =>
    queryOptions({ queryKey: adminKeys.userList(filters), queryFn: () => getData<Paginated<User>>('/admin/users', { ...filters }) }),
  user: (id?: string) => queryOptions({ queryKey: adminKeys.user(id ?? ''), queryFn: () => getData<User>(`/admin/users/${id}`), enabled: !!id }),
  wiki: (query: string) =>
    queryOptions({
      queryKey: adminKeys.resources('wiki', query),
      queryFn: () => getData<WikiSummary>('/admin/resources/search-wikipedia', { query }),
      enabled: query.length >= 3,
      retry: false,
    }),
  images: (query: string) =>
    queryOptions({
      queryKey: adminKeys.resources('images', query),
      queryFn: () => getData<{ unsplash: ImageResult[]; wikimedia: CommonsResult[] }>('/admin/resources/search-images', { query }),
      enabled: query.length >= 3,
    }),
}
