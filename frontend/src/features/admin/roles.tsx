import { Badge } from '@/shared/ui/Feedback'
import type { Role } from '@/shared/api/types'

export const ROLES: Record<Role, string> = { tourist: 'Viajero', socio: 'Socio', admin: 'Administrador' }

/** Pestaña de rol: tinta para administradores, q'olle (solo relleno) para socios */
export function RoleBadge({ role }: { role: Role }) {
  const cls = role === 'admin' ? 'border-2 border-ink bg-ink text-white' : role === 'socio' ? 'border-2 border-ink bg-qolle text-ink' : undefined
  return <Badge className={cls}>{ROLES[role]}</Badge>
}
