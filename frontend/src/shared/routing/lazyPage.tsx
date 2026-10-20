import { lazy, Suspense, type ComponentType } from 'react'
import { Spinner } from '@/shared/ui/Feedback'

/**
 * Página con carga diferida: cada pantalla queda en su propio chunk,
 * así la portada no descarga los paneles. `name` es el export del módulo ('default' o uno nombrado).
 */
export function lazyPage<M extends object>(loader: () => Promise<M>, name: keyof M & string, props?: object) {
  const Component = lazy(async () => ({ default: (await loader())[name] as ComponentType }))
  return (
    <Suspense fallback={<Spinner />}>
      <Component {...(props as object)} />
    </Suspense>
  )
}
