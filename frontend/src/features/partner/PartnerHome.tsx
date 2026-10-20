import { useQuery } from '@tanstack/react-query'
import { Pencil, Plus } from 'lucide-react'
import { Link } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { PageHeader } from '@/shared/ui/PageHeader'
import { StitchRow, Tocapu } from '@/shared/textile/Weave'
import { StatusBadge } from '@/shared/ui/Feedback'
import { IconAction, PanelCard, StatTile, TEXT_LINK } from '@/shared/ui/Panel'
import { CONTENT, imageOf, titleOf } from '@/shared/config/content'
import { formatDate } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { bookingQueries } from '@/features/bookings/api'
import { partnerQueries } from '@/features/partner/api'
import { MANAGED } from '@/features/partner/managed'

export function PartnerHome() {
  useDocumentTitle('Panel de socio')
  const { user } = useAuth()
  const stats = useQuery(partnerQueries.stats())
  const recent = useQuery(partnerQueries.recent())
  const bookings = useQuery(bookingQueries.partner('pending', 1))

  return (
    <>
      <PageHeader
        title={user?.role === 'admin' ? 'Contenido de la plataforma' : 'Tu negocio en Turiscovery'}
        description={user?.role === 'admin' ? 'Como administrador ves y gestionas el contenido de todos los socios.' : 'Publica y actualiza lo que ofreces. Los cambios se ven en el sitio en unos minutos.'}
      />
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {(['locales', 'experiencias', 'eventos', 'promociones'] as const).map((t) => (
          <StatTile key={t} to={`/dashboard/${t}`} value={stats.data?.[t] ?? '–'} label={CONTENT[t].plural} motif={CONTENT[t].motif} dye={CONTENT[t].dye} />
        ))}
      </div>

      <section className="mt-8 grid gap-6 xl:grid-cols-2">
        <PanelCard
          title={
            <span className="flex items-center gap-2">
              Reservas por confirmar
              {bookings.data?.total ? <span className="rounded-full bg-sun px-2 text-sm font-extrabold">{bookings.data.total}</span> : null}
            </span>
          }
          action={<Link to="/dashboard/reservas" className={TEXT_LINK}>Ver todas</Link>}
          bodyClassName="py-2"
        >
          {bookings.isLoading ? (
            <div className="skeleton my-3 h-24" />
          ) : bookings.data?.data.length ? (
            <ul className="flex flex-col divide-y-2 divide-line">
              {bookings.data.data.slice(0, 5).map((b) => (
                <li key={b.id} className="flex items-center gap-3 py-3">
                  <Tocapu motif="olas" dye="anil" size={24} />
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-bold">{b.experiencia?.title}</p>
                    <p className="text-sm text-ink-soft">{b.user?.name} · {formatDate(b.booking_date)} · {b.participants} pers.</p>
                  </div>
                  <StatusBadge status="pending" />
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-3 text-ink-soft">No tienes reservas pendientes. Te avisamos aquí cuando llegue una.</p>
          )}
        </PanelCard>
        <PanelCard title="Publicado recientemente" bodyClassName="py-2">
          {recent.isLoading ? (
            <div className="skeleton my-3 h-24" />
          ) : recent.data?.length ? (
            <ul className="flex flex-col divide-y-2 divide-line">
              {recent.data.slice(0, 6).map((item) => (
                <li key={`${item.contentType}-${item.id}`} className="flex items-center gap-3 py-3">
                  <img src={imageOf(item, item.contentType)} alt="" className="size-12 rounded-[var(--radius-chip)] border-2 border-ink object-cover" />
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-bold">{titleOf(item as never)}</p>
                    <p className="text-sm text-ink-soft">{CONTENT[item.contentType]?.singular} · {formatDate(item.created_at)}</p>
                  </div>
                  <IconAction label="Editar" to={`/dashboard/${item.contentType}/${item.id}`}>
                    <Pencil className="size-4.5" />
                  </IconAction>
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-3 text-ink-soft">Aún no publicas nada. Empieza registrando tu local.</p>
          )}
        </PanelCard>
      </section>

      <StitchRow dye="anil" className="mt-10" />
      <section aria-labelledby="crear" className="mt-8">
        <h2 id="crear" className="t-h3 mb-4">Publicar algo nuevo</h2>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
          {MANAGED.map((t) => (
            <Link key={t} to={`/dashboard/${t}/nuevo`} className="card flex min-h-14 items-center gap-3 p-3 font-bold transition-colors hover:bg-sky">
              <Tocapu motif={CONTENT[t].motif} dye={CONTENT[t].dye} size={28} />
              <span className="flex-1">{CONTENT[t].singular}</span>
              <Plus className="size-4.5 text-ink-soft" aria-hidden />
            </Link>
          ))}
        </div>
      </section>
    </>
  )
}
