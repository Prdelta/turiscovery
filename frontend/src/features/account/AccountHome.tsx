import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { SaveForTripButton } from '@/features/trip/SaveForTripButton'
import { PageHeader } from '@/shared/ui/PageHeader'
import { StitchRow, Tocapu } from '@/shared/textile/Weave'
import { StatTile, TEXT_LINK } from '@/shared/ui/Panel'
import { ButtonLink } from '@/shared/ui/Button'
import { Alert } from '@/shared/ui/Feedback'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountQueries } from '@/features/account/api'

export function AccountHome() {
  useDocumentTitle('Mi cuenta')
  const { user } = useAuth()
  const { data } = useQuery(accountQueries.profile())
  const cards = [
    { to: '/cuenta/favoritos', label: 'Favoritos', value: data?.favorites_count, motif: 'estrella', dye: 'cochinilla' },
    { to: '/cuenta/reservas', label: 'Reservas', value: data?.bookings_count, motif: 'olas', dye: 'anil' },
    { to: '/cuenta/eventos', label: 'Asistencias', value: data?.event_attendances_count, motif: 'chakana', dye: 'cochinilla' },
    { to: '/cuenta/cupones', label: 'Cupones', value: data?.coupons_count, motif: 'ojo', dye: 'chilca' },
    { to: '/cuenta/resenas', label: 'Reseñas', value: data?.reviews_count, motif: 'sol', dye: 'solOscuro' },
  ] as const
  return (
    <>
      <PageHeader title={`Hola, ${user?.name.split(' ')[0]}`} description="Todo lo que guardaste y reservaste en Turiscovery." />
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
        {cards.map((c) => (
          <StatTile key={c.to} to={c.to} value={c.value ?? '–'} label={c.label} motif={c.motif} dye={c.dye} />
        ))}
      </div>
      {(user?.role === 'socio' || user?.role === 'admin') && (
        <div className="mt-6">
        <Alert tone="info">
          Tienes una cuenta de {user.role === 'admin' ? 'administrador' : 'socio'}. Gestiona tu contenido desde el{' '}
          <Link to={user.role === 'admin' ? '/admin' : '/dashboard'} className={TEXT_LINK}>panel</Link>.
        </Alert>
        </div>
      )}
      <section className="card mt-8 flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
        <Tocapu motif="isla" dye="lago" size={40} />
        <div className="flex-1">
          <h2 className="t-h3">¿Vas al lago o a las islas?</h2>
          <p className="mt-1 text-sm text-ink-soft">Allí casi no hay señal. Guarda tus cupones, reservas y tu plan antes de salir.</p>
        </div>
        <SaveForTripButton variant="primary" label="Guardar para el viaje" />
      </section>
      <StitchRow dye="cochinilla" className="mt-8" />
      <div className="mt-8 flex flex-wrap gap-3">
        <ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>
        <ButtonLink to="/eventos" variant="secondary">Ver próximos eventos</ButtonLink>
      </div>
    </>
  )
}
