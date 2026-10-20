import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { CouponCard } from '@/features/promotions/CouponCard'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Tocapu } from '@/shared/textile/Weave'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { EmptyState, Pagination, Spinner } from '@/shared/ui/Feedback'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountKeys, accountQueries } from '@/features/account/api'

export function CouponsPage() {
  useDocumentTitle('Cupones')
  const [page, setPage] = useState(1)
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery(accountQueries.coupons(page))
  const redeem = useMutation({
    mutationFn: (id: number) => api.post(`/user-coupons/${id}/redeem`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: accountKeys.all })
      notify('Cupón marcado como usado')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader title="Cupones" description="Muestra el código en el local al pagar. Márcalo como usado cuando lo canjees." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="grid gap-4 sm:grid-cols-2">
          {data.data.map((c) => (
            <CouponCard
              key={c.id}
              coupon={c}
              action={
                c.status === 'available' && (
                  <Button size="sm" variant="secondary" loading={redeem.isPending && redeem.variables === c.id} loadingText="Marcando…" onClick={() => confirm('¿Ya usaste este cupón en el local?') && redeem.mutate(c.id)}>
                    Marcar como usado
                  </Button>
                )
              }
            />
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
