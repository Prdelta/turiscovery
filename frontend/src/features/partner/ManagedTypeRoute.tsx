import type { ReactNode } from 'react'
import { useParams } from 'react-router'
import { NotFoundPage } from '@/features/info/NotFoundPage'
import { isManaged } from './managed'

/** Rutas con :type: un tipo que el socio no gestiona (/dashboard/lo-que-sea) es una página que no existe */
export function ManagedTypeRoute({ children }: { children: ReactNode }) {
  const { type } = useParams()
  return isManaged(type) ? children : <NotFoundPage />
}
