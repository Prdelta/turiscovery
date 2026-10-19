import { keepPreviousData, useQuery } from '@tanstack/react-query'
import { useEffect, useState } from 'react'
import { getData, type Paginated } from './api'
import type { ContentType } from './content'
import type { AnyContent, Danza, GalleryPhoto } from './types'

export function useContentList<T = AnyContent>(type: ContentType, params: Record<string, unknown> = {}, enabled = true) {
  return useQuery({
    queryKey: ['list', type, params],
    queryFn: () => getData<Paginated<T>>(`/${type}`, params),
    placeholderData: keepPreviousData,
    enabled,
  })
}

export function useContentItem<T = AnyContent>(type: ContentType, id?: string | number) {
  return useQuery({
    queryKey: ['item', type, String(id)],
    queryFn: () => getData<T>(`/${type}/${id}`),
    enabled: !!id,
  })
}

export function useDanzas() {
  return useQuery({ queryKey: ['danzas'], queryFn: () => getData<Danza[]>('/candelaria-danzas'), staleTime: 300_000 })
}

export function useGallery() {
  return useQuery({ queryKey: ['gallery'], queryFn: () => getData<GalleryPhoto[]>('/candelaria-gallery'), staleTime: 300_000 })
}

export function useDebounced<T>(value: T, delay = 350) {
  const [debounced, setDebounced] = useState(value)
  useEffect(() => {
    const t = setTimeout(() => setDebounced(value), delay)
    return () => clearTimeout(t)
  }, [value, delay])
  return debounced
}

export function useDocumentTitle(title?: string) {
  useEffect(() => {
    document.title = title ? `${title} · Turiscovery` : 'Turiscovery · Puno'
  }, [title])
}
