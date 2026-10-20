import { useQuery } from '@tanstack/react-query'
import { CalendarDays, Compass, Mountain, Ticket, Wifi, WifiOff } from 'lucide-react'
import { Link } from 'react-router'
import { useAuth } from '../auth/AuthContext'
import { SaveForTripButton } from '../components/OfflineTools'
import { StitchRow, Tocapu } from '../components/textile/Weave'
import { ButtonLink } from '../components/ui/Button'
import { StatePanel } from '../components/ui/Feedback'
import { dayDate, loadPlan } from '../lib/altitude'
import { getData, type Paginated } from '../lib/api'
import { CONTENT, imageOf, titleOf, typeFromModelClass } from '../lib/content'
import { formatDate } from '../lib/format'
import { useDocumentTitle } from '../lib/hooks'
import { lastSavedAt, useOnline } from '../lib/offline'
import type { AnyContent, Favorite } from '../lib/types'

/**
 * "Viaje": lo que el viajero lleva consigo. Agrupa lo guardado para usar sin señal,
 * el plan de aclimatación y los favoritos (pestaña de la barra inferior en móvil).
 */
export default function TripPage() {
  useDocumentTitle('Mi viaje')
  const { user } = useAuth()
  const online = useOnline()
  const plan = loadPlan()
  const savedAt = lastSavedAt()
  const dayFmt = new Intl.DateTimeFormat('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })

  const favorites = useQuery({
    queryKey: ['favorites', 1],
    enabled: !!user,
    queryFn: () => getData<Paginated<Favorite>>('/favorites', { page: 1 }),
  })
  const favItems = (favorites.data?.data ?? []).filter((f) => f.favoritable && typeFromModelClass(f.favoritable_type))

  return (
    <div className="container-page py-10 sm:py-14">
      <h1 className="t-h1">Mi viaje</h1>
      <p className="prose-measure mt-2 text-ink-soft">Lo que llevas contigo a Puno: tu plan, tus favoritos y lo guardado para usar en el lago, donde casi no hay señal.</p>

      <div className="mt-8 grid gap-4 lg:grid-cols-2">
        <section aria-labelledby="sin-conexion" className="card flex flex-col gap-4 p-6">
          <div className="flex items-center gap-3">
            {online ? <Wifi className="size-6 text-mint-dark" aria-hidden /> : <WifiOff className="size-6 text-sun-dark" aria-hidden />}
            <h2 id="sin-conexion" className="t-h3">Para usar sin conexión</h2>
          </div>
          <p className="text-ink-soft">
            {savedAt
              ? `Guardaste los datos del viaje el ${formatDate(savedAt.toISOString())}. Vuelve a guardar antes de salir para tener lo último.`
              : 'Guarda tus cupones, reservas, tu plan y la información de las islas antes de ir al lago.'}
          </p>
          <div>
            <SaveForTripButton variant="primary" />
          </div>
        </section>

        <section aria-labelledby="mi-plan" className="card flex flex-col gap-4 p-6">
          <div className="flex items-center gap-3">
            <Mountain className="size-6 text-primary" aria-hidden />
            <h2 id="mi-plan" className="t-h3">Mi plan</h2>
          </div>
          {plan ? (
            <>
              <p className="text-ink-soft first-letter:uppercase">
                {plan.days} día{plan.days > 1 ? 's' : ''} desde el {dayFmt.format(dayDate(plan.arrival, 0))} ·{' '}
                {Object.keys(plan.experiences).length} experiencias · {plan.events.length} eventos
              </p>
              <div>
                <ButtonLink to="/planificador" variant="secondary">Abrir mi plan</ButtonLink>
              </div>
            </>
          ) : (
            <>
              <p className="text-ink-soft">Arma tu plan según la altura: lo suave al llegar, las islas lejanas cuando ya estés aclimatado.</p>
              <div>
                <ButtonLink to="/planificador" variant="secondary">Armar mi plan</ButtonLink>
              </div>
            </>
          )}
        </section>
      </div>

      <StitchRow dye="cochinilla" className="mt-12" />

      <section aria-labelledby="favoritos" className="mt-8">
        <div className="mb-4 flex items-end justify-between gap-4">
          <h2 id="favoritos" className="t-h2">Favoritos</h2>
          {user && favItems.length > 0 && (
            <Link to="/cuenta/favoritos" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Ver todos</Link>
          )}
        </div>
        {!user ? (
          <StatePanel kind="empty" title="Inicia sesión para guardar favoritos" action={<ButtonLink to="/login?next=%2Fviaje">Iniciar sesión</ButtonLink>}>
            Tus favoritos, reservas y cupones quedan en tu cuenta y también se guardan para usarlos sin conexión.
          </StatePanel>
        ) : favorites.isLoading ? (
          <div className="skeleton h-24" />
        ) : favItems.length ? (
          <ul className="grid gap-3 sm:grid-cols-2">
            {favItems.slice(0, 6).map((f) => {
              const type = typeFromModelClass(f.favoritable_type)!
              const item = f.favoritable as AnyContent
              return (
                <li key={f.id}>
                  <Link to={`${CONTENT[type].path}/${item.id}`} className="card flex items-center gap-3 p-3 hover:bg-sky">
                    <img src={imageOf(item, type)} alt="" className="size-16 rounded-[var(--radius-chip)] object-cover" />
                    <span className="min-w-0 flex-1">
                      <span className="block truncate font-bold">{titleOf(item as never)}</span>
                      <span className="flex items-center gap-1.5 text-sm text-ink-soft">
                        <Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={16} framed={false} /> {CONTENT[type].singular}
                      </span>
                    </span>
                  </Link>
                </li>
              )
            })}
          </ul>
        ) : (
          <StatePanel kind="empty" title="Aún no guardas experiencias" icon={<Tocapu motif="olas" dye="anil" size={40} framed={false} />} action={<ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>}>
            Toca el corazón en cualquier experiencia y la verás aquí, incluso sin señal.
          </StatePanel>
        )}
      </section>

      {user && (
        <nav aria-label="Atajos" className="mt-10 grid gap-3 sm:grid-cols-3">
          {[
            { to: '/cuenta/reservas', label: 'Mis reservas', icon: Compass },
            { to: '/cuenta/cupones', label: 'Mis cupones', icon: Ticket },
            { to: '/cuenta/eventos', label: 'Eventos a los que voy', icon: CalendarDays },
          ].map(({ to, label, icon: Icon }) => (
            <Link key={to} to={to} className="card flex min-h-14 items-center gap-3 px-4 font-bold hover:bg-sky">
              <Icon className="size-5 text-primary" aria-hidden /> {label}
            </Link>
          ))}
        </nav>
      )}
    </div>
  )
}
