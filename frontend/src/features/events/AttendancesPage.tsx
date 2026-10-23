import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { Link } from 'react-router'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Tocapu } from '@/shared/textile/Weave'
import { ButtonLink } from '@/shared/ui/Button'
import { EmptyState, Pagination, Spinner, StatusBadge } from '@/shared/ui/Feedback'
import { formatDate, formatTime } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountQueries } from '@/features/account/api'

export function AttendancesPage() {
  useDocumentTitle('Asistencias')
  const [page, setPage] = useState(1)
  const { data, isLoading } = useQuery(accountQueries.attendances(page))
  return (
    <>
      <PageHeader title="Asistencias a eventos" description="Eventos en los que confirmaste que irás o marcaste que quizás vayas." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="flex flex-col gap-3">
          {data.data.map((a) => (
            <article key={a.id} className="card flex items-stretch overflow-hidden">
              <div className="flex w-20 shrink-0 flex-col items-center justify-center border-r-2 border-ink bg-cochinilla p-2 text-white">
                {a.evento ? (
                  <>
                    <span className="t-label">{new Date(a.evento.start_time).toLocaleDateString('es-PE', { month: 'short' }).replace('.', '')}</span>
                    <span className="font-display text-3xl font-extrabold leading-none">{new Date(a.evento.start_time).getDate()}</span>
                  </>
                ) : (
                  <Tocapu motif="chakana" dye="cochinilla" size={28} />
                )}
              </div>
              <div className="flex min-w-0 flex-1 flex-wrap items-center gap-3 p-4">
              <div className="min-w-0 flex-1">
                <Link to={`/eventos/${a.evento_id}`} className="t-h3 hover:text-primary-dark">{a.evento?.title ?? 'Evento'}</Link>
                {a.evento && <p className="mt-1 text-sm text-ink-soft first-letter:uppercase">{formatDate(a.evento.start_time)}, {formatTime(a.evento.start_time)}</p>}
              </div>
              <StatusBadge status={a.status === 'confirmed' ? 'confirmed' : 'maybe'} label={a.status === 'confirmed' ? 'Voy' : 'Quizás'} />
              </div>
            </article>
          ))}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="Sin eventos confirmados" icon={<Tocapu motif="chakana" dye="cochinilla" size={40} framed={false} />} action={<ButtonLink to="/eventos">Ver eventos</ButtonLink>}>
          Confirma tu asistencia desde la página de un evento.
        </EmptyState>
      )}
    </>
  )
}
