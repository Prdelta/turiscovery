import { useEffect, useState } from 'react'

export function useDebounced<T>(value: T, delay = 350) {
  const [debounced, setDebounced] = useState(value)
  useEffect(() => {
    const t = setTimeout(() => setDebounced(value), delay)
    return () => clearTimeout(t)
  }, [value, delay])
  return debounced
}

/** Fecha y hora en que se montó el componente: estable entre renders */
export function useNow() {
  const [now] = useState(() => new Date())
  return now
}

export function useDocumentTitle(title?: string) {
  useEffect(() => {
    document.title = title ? `${title} · Ñan Puno` : 'Ñan Puno'
  }, [title])
}
