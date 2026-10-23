import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { CouponCard } from '@/features/promotions/CouponCard'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Tocapu } from '@/shared/textile/Weave'
import { ButtonLink } from '@/shared/ui/Button'
import { EmptyState, Pagination, Spinner } from '@/shared/ui/Feedback'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountQueries } from '@/features/account/api'

/** Cupones del viajero. El canje lo registra el local al cobrar (ver CouponRedeemPage del socio). */
export function CouponsPage() {
  useDocumentTitle('Cupones')
  const [page, setPage] = useState(1)
  const { data, isLoading } = useQuery(accountQueries.coupons(page))

  return (
    <>
      <PageHeader title="Cupones" description="Muestra el código en el local al pagar: allí lo validan y queda marcado como usado." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="grid gap-4 sm:grid-cols-2">
          {data.data.map((c) => (
            <CouponCard key={c.id} coupon={c} />
          ))}
          <div className="sm:col-span-2">
            <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
          </div>
        </div>
      ) : (
        <EmptyState title="No tienes cupones" icon={<Tocapu motif="ojo" dye="chilca" size={40} framed={false} />} action={<ButtonLink to="/promociones">Ver promociones</ButtonLink>}>
          Obtén un cupón desde cualquier promoción vigente.
        </EmptyState>
      )}
    </>
  )
}
