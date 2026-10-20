import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { ContentCard } from '@/features/catalog/components/ContentCard'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Tocapu } from '@/shared/textile/Weave'
import { ButtonLink } from '@/shared/ui/Button'
import { EmptyState, ErrorState, Pagination, Spinner } from '@/shared/ui/Feedback'
import { parseApiError } from '@/shared/api/client'
import { typeFromModelClass } from '@/shared/config/content'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { AnyContent } from '@/shared/api/types'
import { catalogQueries } from '@/features/catalog/api'

export function FavoritesPage() {
  useDocumentTitle('Favoritos')
  const [page, setPage] = useState(1)
  const { data, isLoading, isError, error, refetch } = useQuery(catalogQueries.favorites(page))
  const items = (data?.data ?? []).filter((f) => f.favoritable && typeFromModelClass(f.favoritable_type))

  return (
    <>
      <PageHeader title="Favoritos" description="Lo que guardaste para no perderlo de vista." />
      {isLoading ? (
        <Spinner />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : items.length ? (
        <>
          <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            {items.map((f) => (
              <ContentCard key={f.id} item={f.favoritable as AnyContent} type={typeFromModelClass(f.favoritable_type)!} />
            ))}
          </div>
          <Pagination page={data!.current_page} lastPage={data!.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState title="Aún no guardas nada" icon={<Tocapu motif="estrella" dye="cochinilla" size={40} framed={false} />} action={<ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>}>
          Toca el corazón en cualquier experiencia, evento, promoción o local para guardarlo aquí.
        </EmptyState>
      )}
    </>
  )
}
