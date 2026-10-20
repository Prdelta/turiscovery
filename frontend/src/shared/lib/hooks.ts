import { useEffect, useState } from 'react'

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
