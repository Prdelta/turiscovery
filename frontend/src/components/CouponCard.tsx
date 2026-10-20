import type { ReactNode } from 'react'
import { Link } from 'react-router'
import { formatDate } from '../lib/format'
import type { UserCoupon } from '../lib/types'
import { discountText } from './ContentCard'
import { StitchRow, Tocapu } from './textile/Weave'
import { StatusBadge } from './ui/Feedback'

/**
 * Cupón de la guía Telar: cabecera chilca con el descuento, código grande
 * para mostrar en caja y su estado. Funciona sin conexión si se guardó antes.
 */
export function CouponCard({ coupon, action }: { coupon: UserCoupon; action?: ReactNode }) {
  const promo = coupon.promocion
  const expired = coupon.status === 'expired' || (coupon.expires_at ? new Date(coupon.expires_at) < new Date() : false)
  const status = coupon.status === 'used' ? 'used' : expired ? 'expired' : 'available'

  return (
    <article className="card flex flex-col overflow-hidden">
      <div className="flex items-start justify-between gap-3 bg-chilca p-5 text-white">
        <div>
          <p className="font-display text-4xl font-extrabold leading-none">
            {promo ? (promo.discount_type === 'percentage' ? `${promo.discount_percentage ?? 0}% menos` : discountText(promo)) : 'Cupón'}
          </p>
          {promo && (
            <Link to={`/promociones/${coupon.promocion_id}`} className="mt-2 block font-bold text-white underline-offset-4 hover:underline">
              {promo.title}
            </Link>
          )}
        </div>
        <Tocapu motif="ojo" dye="chilca" size={44} />
      </div>
      <StitchRow dye="chilca" />
      <div className="flex flex-1 flex-col gap-3 p-5">
        <p className="t-small text-ink-soft">Muestra este código en caja</p>
        <p className="font-mono text-3xl font-bold tracking-[0.15em] break-all">{coupon.coupon_code}</p>
        <div className="flex flex-wrap items-center gap-2">
          <StatusBadge status={status} />
          <span className="t-small text-ink-soft">
            {coupon.status === 'used' && coupon.used_at
              ? `Usado el ${formatDate(coupon.used_at)}`
              : coupon.expires_at
                ? `Vence el ${formatDate(coupon.expires_at)}`
                : 'Funciona sin conexión'}
          </span>
        </div>
        {action && <div className="mt-auto pt-2">{action}</div>}
      </div>
    </article>
  )
}
