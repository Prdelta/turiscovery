import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState, type FormEvent } from 'react'
import { api, getData, parseApiError, type Paginated } from '@/shared/api/client'
import { formatDate } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { StitchRow, Tocapu } from '@/shared/textile/Weave'
import { Button } from '@/shared/ui/Button'
import { Alert, EmptyState, Pagination, Spinner, StatusBadge } from '@/shared/ui/Feedback'
import { TextField } from '@/shared/ui/Field'
import { PageHeader } from '@/shared/ui/PageHeader'
import { PanelCard } from '@/shared/ui/Panel'
import { useToast } from '@/shared/ui/Toast'
import type { Promocion, UserCoupon } from '@/shared/api/types'

/** Cupón tal como lo devuelve /partner/coupons/lookup */
interface PartnerCoupon {
  id: number
  coupon_code: string
  status: UserCoupon['status']
  claimed_at?: string | null
  used_at?: string | null
  expires_at?: string | null
  user?: { id: number; name: string } | null
  promocion?: (Pick<Promocion, 'id' | 'title' | 'discount_type' | 'discount_percentage' | 'discount_amount' | 'original_price' | 'final_price' | 'terms_conditions'> & {
    locale?: { id: number; name: string } | null
  }) | null
}

const redeemedKey = (page: number) => ['partner', 'coupons', page] as const

function discountLabel(p: NonNullable<PartnerCoupon['promocion']>) {
  if (p.discount_type === '2x1') return '2x1'
  if (p.discount_type === 'percentage') return `${p.discount_percentage ?? 0}% de descuento`
  return `S/ ${Number(p.discount_amount ?? 0).toFixed(2)} de descuento`
}

/**
 * Canje en el local: el viajero muestra su código, el socio lo busca,
 * revisa la promoción y lo marca como usado. Así nadie usa un cupón dos veces.
 */
export function CouponRedeemPage() {
  useDocumentTitle('Canjear cupón')
  const notify = useToast()
  const qc = useQueryClient()
  const [code, setCode] = useState('')
  const [page, setPage] = useState(1)

  const lookup = useMutation({
    mutationFn: (c: string) => getData<PartnerCoupon>('/partner/coupons/lookup', { code: c }),
  })
  const redeem = useMutation({
    mutationFn: async (c: string) => (await api.post<{ message: string; data: PartnerCoupon }>('/partner/coupons/redeem', { code: c })).data,
    onSuccess: (res) => {
      notify(res.message)
      lookup.reset()
      setCode('')
      void qc.invalidateQueries({ queryKey: ['partner', 'coupons'] })
    },
  })
  const redeemed = useQuery({
    queryKey: redeemedKey(page),
    queryFn: () => getData<Paginated<UserCoupon & { user?: { id: number; name: string } }>>('/partner/coupons', { page }),
  })

  const onSearch = (e: FormEvent) => {
    e.preventDefault()
    redeem.reset()
    if (code.trim()) lookup.mutate(code.trim())
  }

  const coupon = lookup.data
  const error = lookup.error ?? redeem.error

  return (
    <>
      <PageHeader title="Canjear cupón" description="Pide al viajero su código, búscalo y márcalo como usado al cobrar." />
      <div className="flex max-w-2xl flex-col gap-6">
        <PanelCard title="Buscar código">
          <form onSubmit={onSearch} className="flex flex-col gap-3 sm:flex-row sm:items-end">
            <TextField
              label="Código del cupón"
              placeholder="TURISCO-XXXXXXXX"
              value={code}
              onChange={(e) => setCode(e.target.value.toUpperCase())}
              autoComplete="off"
              autoCapitalize="characters"
              spellCheck={false}
              className="font-mono tracking-widest"
              wrapClassName="flex-1"
              required
            />
            <Button type="submit" loading={lookup.isPending} loadingText="Buscando…">Buscar</Button>
          </form>
          {error && (
            <div className="mt-4">
              <Alert tone="error">{parseApiError(error).message}</Alert>
            </div>
          )}
        </PanelCard>

        {coupon && (
          <article className="card overflow-hidden">
            <div className="flex items-start justify-between gap-3 bg-chilca p-5 text-white">
              <div>
                <p className="font-display text-3xl font-extrabold leading-none">{coupon.promocion ? discountLabel(coupon.promocion) : 'Cupón'}</p>
                <p className="mt-2 font-bold">{coupon.promocion?.title}</p>
                {coupon.promocion?.locale && <p className="text-sm">{coupon.promocion.locale.name}</p>}
              </div>
              <Tocapu motif="ojo" dye="chilca" size={44} />
            </div>
            <StitchRow dye="chilca" />
            <div className="flex flex-col gap-3 p-5">
              <p className="font-mono text-2xl font-bold tracking-[0.15em] break-all">{coupon.coupon_code}</p>
              <dl className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-sm">
                <dt className="text-ink-soft">Viajero</dt>
                <dd className="font-bold">{coupon.user?.name ?? '—'}</dd>
                {coupon.promocion?.final_price != null && (
                  <>
                    <dt className="text-ink-soft">Precio final</dt>
                    <dd className="font-bold">S/ {Number(coupon.promocion.final_price).toFixed(2)}</dd>
                  </>
                )}
                {coupon.expires_at && (
                  <>
                    <dt className="text-ink-soft">Vence</dt>
                    <dd>{formatDate(coupon.expires_at)}</dd>
                  </>
                )}
              </dl>
              {coupon.promocion?.terms_conditions && <p className="text-sm text-ink-soft">Condiciones: {coupon.promocion.terms_conditions}</p>}
              <div className="flex flex-wrap items-center gap-3 pt-2">
                <StatusBadge status={coupon.status} />
                {coupon.status === 'available' ? (
                  <Button loading={redeem.isPending} loadingText="Canjeando…" onClick={() => redeem.mutate(coupon.coupon_code)}>
                    Marcar como usado
                  </Button>
                ) : (
                  <span className="text-sm font-bold text-danger">
                    {coupon.status === 'used' ? `Ya se usó${coupon.used_at ? ` el ${formatDate(coupon.used_at)}` : ''}.` : 'Este cupón venció.'}
                  </span>
                )}
              </div>
            </div>
          </article>
        )}

        <PanelCard title="Canjeados recientemente">
          {redeemed.isLoading ? (
            <Spinner />
          ) : redeemed.data?.data.length ? (
            <>
              <ul className="divide-y-2 divide-line">
                {redeemed.data.data.map((c) => (
                  <li key={c.id} className="flex flex-wrap items-baseline justify-between gap-2 py-3">
                    <span>
                      <span className="font-mono font-bold">{c.coupon_code}</span>
                      <span className="block text-sm text-ink-soft">{c.promocion?.title} · {c.user?.name}</span>
                    </span>
                    <span className="text-sm text-ink-soft">{c.used_at ? formatDate(c.used_at) : ''}</span>
                  </li>
                ))}
              </ul>
              <Pagination page={redeemed.data.current_page} lastPage={redeemed.data.last_page} onChange={setPage} />
            </>
          ) : (
            <EmptyState title="Aún no canjeaste cupones">Cuando marques un cupón como usado aparecerá aquí.</EmptyState>
          )}
        </PanelCard>
      </div>
    </>
  )
}
