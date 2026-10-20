import { CalendarDays, Clock, MapPin, Star } from 'lucide-react'
import type { ReactNode } from 'react'
import { Link } from 'react-router'
import { CONTENT, EVENT_CATEGORIES, LOCALE_CATEGORIES, imageOf, titleOf, type ContentType } from '@/shared/config/content'
import { formatPrice, formatShortDate, formatTime } from '@/shared/lib/format'
import { placeFor } from '@/shared/config/places'
import type { AnyContent, Evento, Experiencia, Locale, Promocion } from '@/shared/api/types'
import { FavoriteButton } from '@/features/catalog/components/FavoriteButton'
import { TypeTag } from '@/shared/textile/Tags'
import { DYES, Tocapu } from '@/shared/textile/Weave'
import { DifficultyBadge } from '@/shared/ui/Feedback'

/**
 * Tarjetas de contenido de la guía Telar.
 * - Experiencias, locales y Candelaria: tarjeta vertical con foto, pestaña del tipo y favorito.
 * - Eventos y promociones: tarjeta horizontal con un bloque de color (fecha o descuento).
 */
export function ContentCard({ item, type }: { item: AnyContent; type: ContentType }) {
  if (type === 'eventos') return <EventCard evento={item as Evento} />
  if (type === 'promociones') return <PromoCard promo={item as Promocion} />
  return <VerticalCard item={item} type={type} />
}

/** Lugar del lago donde está el contenido (para la pestaña), si tiene coordenadas */
function placeName(item: { latitude?: number | null; longitude?: number | null }) {
  const p = placeFor(item.latitude, item.longitude)
  return p?.name.replace('Islas de los ', '').replace('Isla ', '')
}

function VerticalCard({ item, type }: { item: AnyContent; type: ContentType }) {
  const meta = CONTENT[type]
  const href = `${meta.path}/${item.id}`
  const located = item as { latitude?: number | null; longitude?: number | null }
  const rating = (item as { average_rating?: number }).average_rating

  return (
    <article className="card group relative flex flex-col overflow-hidden">
      <div className="relative overflow-hidden bg-line">
        <img src={imageOf(item, type)} alt="" loading="lazy" className="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" />
      </div>
      <div className="flex flex-1 flex-col gap-2 border-t-2 border-ink p-4">
        <h3 className="t-h3">
          {/* El enlace cubre toda la tarjeta; el favorito queda por encima */}
          <Link to={href} className="after:absolute after:inset-0 hover:text-primary-dark">
            {titleOf(item as never)}
          </Link>
        </h3>
        <CardMeta item={item} type={type} />
        {rating ? (
          <p className="flex items-center gap-1 text-sm font-bold">
            <Star className="size-4 fill-sun-dark text-sun-dark" aria-hidden /> {rating.toFixed(1)}
          </p>
        ) : null}
        {type === 'experiencias' && <ExperienceFooter e={item as Experiencia} />}
      </div>
      <div className="pointer-events-none absolute inset-x-3 top-3 z-10 flex items-start justify-between gap-2">
        <TypeTag type={type} detail={placeName(located)} />
        <span className="pointer-events-auto">
          <FavoriteButton type={type} id={item.id} compact />
        </span>
      </div>
    </article>
  )
}

function ExperienceFooter({ e }: { e: Experiencia }) {
  const price = formatPrice(e.price_pen)
  return (
    <div className="mt-auto flex items-end justify-between gap-3 pt-2">
      <p>
        <span className="block font-display text-2xl font-extrabold leading-none">{price ?? 'Consultar'}</span>
        {price && price !== 'Gratis' && <span className="t-small text-ink-soft">por persona</span>}
      </p>
      <DifficultyBadge level={e.difficulty} />
    </div>
  )
}

function CardMeta({ item, type }: { item: AnyContent; type: ContentType }) {
  const row = 'flex flex-wrap items-center gap-x-1.5 gap-y-1 text-sm text-ink-soft'
  if (type === 'experiencias') {
    const e = item as Experiencia
    return (
      <>
        {(e.meeting_point || e.duration_hours) && (
          <p className={row}>
            {e.meeting_point && (
              <span className="inline-flex items-center gap-1">
                <MapPin className="size-4 shrink-0" aria-hidden /> {e.meeting_point}
              </span>
            )}
            {e.duration_hours ? (
              <span className="inline-flex items-center gap-1">
                <Clock className="size-4 shrink-0" aria-hidden /> {e.duration_hours} h
              </span>
            ) : null}
          </p>
        )}
        {e.anfitrion && (
          <p className="flex items-center gap-2 text-sm">
            {e.anfitrion.photo_url ? (
              <img src={e.anfitrion.photo_url} alt="" className="size-6 rounded-full border-2 border-ink object-cover" />
            ) : (
              <Tocapu motif="persona" dye="qolle" size={20} />
            )}
            Te recibe <strong className="font-bold">{e.anfitrion.name}</strong>
          </p>
        )}
      </>
    )
  }
  if (type === 'locales') {
    const l = item as Locale
    return (
      <>
        <p className={row}>{LOCALE_CATEGORIES[l.category] ?? l.category}</p>
        {(l.short_address || l.address) && (
          <p className={row}>
            <MapPin className="size-4 shrink-0" aria-hidden /> <span className="line-clamp-1">{l.short_address || l.address}</span>
          </p>
        )}
      </>
    )
  }
  return <p className="line-clamp-2 text-sm text-ink-soft">{item.description}</p>
}

/** Bloque de color a la izquierda + datos a la derecha */
function HorizontalCard({
  href,
  dye,
  block,
  title,
  lines,
  type,
  id,
}: {
  href: string
  dye: keyof typeof DYES
  block: ReactNode
  title: string
  lines: ReactNode[]
  type: ContentType
  id: number
}) {
  return (
    <article className="card group relative flex min-h-36 overflow-hidden">
      <div className="flex w-28 shrink-0 flex-col items-center justify-center gap-1.5 border-r-2 border-ink p-3 text-center text-white" style={{ background: DYES[dye] }}>
        {block}
      </div>
      <div className="flex min-w-0 flex-1 flex-col gap-1 p-4 pr-16">
        <h3 className="t-h3">
          <Link to={href} className="after:absolute after:inset-0 hover:text-primary-dark">
            {title}
          </Link>
        </h3>
        {lines.map((l, i) => (
          <p key={i} className="text-sm text-ink-soft">{l}</p>
        ))}
      </div>
      <div className="absolute right-3 top-3 z-10">
        <FavoriteButton type={type} id={id} compact />
      </div>
    </article>
  )
}

function EventCard({ evento: e }: { evento: Evento }) {
  const d = new Date(e.start_time)
  const month = d.toLocaleDateString('es-PE', { month: 'short' }).replace('.', '')
  const price = formatPrice(e.ticket_price)
  const place = e.locale?.name ?? e.short_address
  return (
    <HorizontalCard
      href={`/eventos/${e.id}`}
      dye="cochinilla"
      type="eventos"
      id={e.id}
      title={e.title}
      block={
        <>
          <span className="t-label">{month}</span>
          <span className="font-display text-4xl font-extrabold leading-none">{String(d.getDate()).padStart(2, '0')}</span>
          <Tocapu motif="chakana" dye="cochinilla" size={24} />
        </>
      }
      lines={[
        <span key="w" className="inline-flex items-center gap-1.5">
          <CalendarDays className="size-4" aria-hidden /> {formatTime(e.start_time)} · {EVENT_CATEGORIES[e.category] ?? e.category}
        </span>,
        ...(place
          ? [
              <span key="p" className="inline-flex items-center gap-1.5">
                <MapPin className="size-4 shrink-0" aria-hidden /> {place}
              </span>,
            ]
          : []),
        ...(price ? [<strong key="$" className="text-ink">{price}</strong>] : []),
      ]}
    />
  )
}

// eslint-disable-next-line react-refresh/only-export-components
export function discountText(p: Pick<Promocion, 'discount_type' | 'discount_percentage' | 'discount_amount'>) {
  if (p.discount_type === '2x1') return '2×1'
  if (p.discount_type === 'percentage') return `-${p.discount_percentage ?? 0}%`
  return `-${formatPrice(p.discount_amount) ?? ''}`
}

function PromoCard({ promo: p }: { promo: Promocion }) {
  return (
    <HorizontalCard
      href={`/promociones/${p.id}`}
      dye="chilca"
      type="promociones"
      id={p.id}
      title={p.title}
      block={
        <>
          <Tocapu motif="ojo" dye="chilca" size={28} />
          <span className="font-display text-3xl font-extrabold leading-none">{discountText(p)}</span>
        </>
      }
      lines={[
        ...(p.locale ? [<span key="l">{p.locale.name}</span>] : []),
        <span key="v">Vence el {formatShortDate(p.end_date)}</span>,
      ]}
    />
  )
}
