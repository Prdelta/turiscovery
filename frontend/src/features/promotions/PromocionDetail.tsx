import { useMutation } from '@tanstack/react-query'
import { CalendarDays, Ticket } from 'lucide-react'
import { useState } from 'react'
import { useAuth } from '@/shared/auth/AuthContext'
import { Button } from '@/shared/ui/Button'
import { Alert, Badge } from '@/shared/ui/Feedback'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { daysUntil, formatDate, formatPrice } from '@/shared/lib/format'
import type { Promocion, UserCoupon } from '@/shared/api/types'
import { AsideCard, DetailShell, DetailState, LocaleLink, LoginPrompt, useDetail } from '@/features/catalog/detail/DetailShell'

export function PromocionDetail() {
  const { data, isLoading, error, refetch } = useDetail<Promocion>('promociones')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const p = data
  const discount =
    p.discount_type === '2x1' ? '2 × 1' : p.discount_type === 'percentage' ? `${p.discount_percentage ?? 0}% de descuento` : `${formatPrice(p.discount_amount)} de descuento`
  return (
    <DetailShell
      item={p}
      type="promociones"
      showMap={false}
      kicker={<Badge className="border-2 border-ink bg-chilca text-white">{discount}</Badge>}
      meta={
        <>
          <p className="flex items-center gap-2"><CalendarDays className="size-4.5" aria-hidden /> Válida del {formatDate(p.start_date)} al {formatDate(p.end_date)}</p>
          <LocaleLink locale={p.locale} />
        </>
      }
      aside={<CouponCard promo={p} discount={discount} />}
    >
      {p.terms_conditions && (
        <section className="mt-10">
          <h2 className="mb-3 t-h2">Términos y condiciones</h2>
          <p className="prose-measure whitespace-pre-line text-ink-soft">{p.terms_conditions}</p>
        </section>
      )}
    </DetailShell>
  )
}

function CouponCard({ promo, discount }: { promo: Promocion; discount: string }) {
  const { user } = useAuth()
  const notify = useToast()
  const [coupon, setCoupon] = useState<UserCoupon | null>(null)
  const [error, setError] = useState<string | null>(null)

  const claim = useMutation({
    mutationFn: async () => (await api.post<{ data: UserCoupon; message: string }>('/user-coupons', { promocion_id: promo.id })).data,
    onSuccess: (res) => {
      setCoupon(res.data)
      notify(res.message)
    },
    onError: (e) => setError(parseApiError(e).message),
  })

  return (
    <AsideCard>
      <p className="font-display text-4xl font-extrabold text-chilca">{discount}</p>
      {promo.original_price && promo.final_price && (
        <p className="text-ink-soft">
          <s>{formatPrice(promo.original_price)}</s> <strong className="text-lg text-ink">{formatPrice(promo.final_price)}</strong>
        </p>
      )}
      <p className="text-sm text-ink-soft">Quedan {Math.max(0, daysUntil(promo.end_date))} días para usarla.</p>
      {coupon ? (
        <div className="rounded-[var(--radius-control)] border-2 border-dashed border-chilca bg-lana p-4 text-center">
          <p className="text-sm text-ink-soft">Tu código</p>
          <p className="font-mono text-3xl font-bold tracking-[0.15em] break-all">{coupon.coupon_code}</p>
          <p className="mt-1 text-xs text-ink-soft">Muéstralo al pagar en el local.</p>
        </div>
      ) : user ? (
        <Button onClick={() => claim.mutate()} loading={claim.isPending} icon={<Ticket className="size-4.5" />}>
          Obtener cupón
        </Button>
      ) : (
        <LoginPrompt action="obtener el cupón" />
      )}
      {error && <Alert tone="error">{error}</Alert>}
    </AsideCard>
  )
}
