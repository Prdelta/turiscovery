import type { ReactNode } from 'react'
import { Navigate, useLocation } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import type { Role } from '@/shared/api/types'
import { ButtonLink } from '@/shared/ui/Button'
import { Spinner, StatePanel } from '@/shared/ui/Feedback'

/** Protege rutas por sesión y rol (con el estado "sin permisos" de la guía) */
export function RequireAuth({ roles, children }: { roles?: Role[]; children: ReactNode }) {
  const { user, loading } = useAuth()
  const location = useLocation()
  if (loading) return <Spinner label="Comprobando tu sesión…" />
  if (!user) return <Navigate to={`/login?next=${encodeURIComponent(location.pathname)}`} replace />
  if (roles && !roles.includes(user.role)) {
    const forPartners = roles.includes('socio')
    return (
      <div className="container-page py-16">
        <StatePanel
          kind="forbidden"
          title={forPartners ? 'Esta sección es para socios' : 'Esta sección es para administradores'}
          action={forPartners ? <ButtonLink to="/contacto" variant="secondary">Ser socio</ButtonLink> : <ButtonLink to="/" variant="secondary">Volver al inicio</ButtonLink>}
        >
          {forPartners
            ? 'Si tienes un negocio en Puno, solicita tu cuenta de socio.'
            : 'Si crees que deberías tener acceso, pide a un administrador que revise tu rol.'}
        </StatePanel>
      </div>
    )
  }
  return <>{children}</>
}
