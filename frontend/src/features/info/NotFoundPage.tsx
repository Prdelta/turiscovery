import { ButtonLink } from '@/shared/ui/Button'
import { useDocumentTitle } from '@/shared/lib/hooks'

export function NotFoundPage() {
  useDocumentTitle('Página no encontrada')
  return (
    <div className="container-page flex flex-col items-start py-24">
      <p className="font-display text-8xl font-extrabold text-primary">404</p>
      <h1 className="mt-4 t-h1">Esta página no existe</h1>
      <p className="mt-3 text-lg text-ink-soft">Puede que el enlace esté mal escrito o que el contenido se haya retirado.</p>
      <div className="mt-8 flex gap-3">
        <ButtonLink to="/">Ir al inicio</ButtonLink>
        <ButtonLink to="/buscar" variant="secondary">Buscar</ButtonLink>
      </div>
    </div>
  )
}
