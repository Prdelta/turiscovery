import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { Link } from 'react-router'
import { PageHeader } from '@/shared/ui/PageHeader'
import { TypeTag } from '@/shared/textile/Tags'
import { Tocapu } from '@/shared/textile/Weave'
import { EmptyState, Pagination, Spinner, Stars } from '@/shared/ui/Feedback'
import { CONTENT, typeFromModelClass } from '@/shared/config/content'
import { formatDate } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountQueries } from '@/features/account/api'

export function MyReviewsPage() {
  useDocumentTitle('Mis reseñas')
  const [page, setPage] = useState(1)
  const { data, isLoading } = useQuery(accountQueries.reviews(page))
  return (
    <>
      <PageHeader title="Mis reseñas" />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="flex flex-col gap-3">
          {data.data.map((r) => {
            const type = typeFromModelClass(r.reviewable_type)
            const name = r.reviewable?.title ?? r.reviewable?.name ?? 'Contenido retirado'
            return (
              <article key={r.id} className="card p-5">
                <p className="flex flex-wrap items-center gap-2 text-sm text-ink-soft">
                  {type && <TypeTag type={type} />}
                  <Stars value={r.rating} /> {formatDate(r.created_at)}
                </p>
                {type && r.reviewable ? (
                  <Link to={`${CONTENT[type].path}/${r.reviewable_id}`} className="t-h3 mt-3 block hover:text-primary-dark">{name}</Link>
                ) : (
                  <p className="mt-2 font-bold">{name}</p>
                )}
                {r.title && <p className="mt-2 font-bold">“{r.title}”</p>}
                {r.comment && <p className="text-ink-soft">{r.comment}</p>}
              </article>
            )
          })}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="Aún no escribes reseñas" icon={<Tocapu motif="sol" dye="solOscuro" size={40} framed={false} />}>
          Después de una experiencia o evento, cuenta cómo te fue desde su página.
        </EmptyState>
      )}
    </>
  )
}
