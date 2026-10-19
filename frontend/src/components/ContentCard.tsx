import { CalendarDays, Clock, MapPin } from 'lucide-react'
import { Link } from 'react-router'
import { CONTENT, DIFFICULTY, EVENT_CATEGORIES, LOCALE_CATEGORIES, imageOf, titleOf, type ContentType } from '../lib/content'
import { formatPrice, formatShortDate, formatTime } from '../lib/format'
import type { AnyContent, Evento, Experiencia, Locale, Promocion } from '../lib/types'
import { FavoriteButton } from './FavoriteButton'
import { Tocapu, WovenBand } from './textile/Weave'

/**
 * Tarjeta de contenido: la foto lleva debajo la faja tejida de su categoría,
 * y el tocapu identifica el tipo sin necesidad de etiquetas.
 */
export function ContentCard({ item, type }: { item: AnyContent; type: ContentType }) {
  const meta = CONTENT[type]
  const href = `${meta.path}/${item.id}`

  return (
    <article className="group relative flex flex-col">
      <Link to={href} className="relative block overflow-hidden rounded-t-[var(--radius-card)] bg-line">
        <img
          src={imageOf(item, type)}
          alt=""
          loading="lazy"
          className="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
        />
        <CardFlag item={item} type={type} />
      </Link>
      <WovenBand motif={meta.motif} dye={meta.dye} height={14} />
      <div className="absolute right-3 top-3">
        <FavoriteButton type={type} id={item.id} compact />
      </div>
      <div className="flex flex-1 flex-col gap-1.5 pt-3.5">
        <p className="flex items-center gap-2 text-xs font-semibold text-ink-soft">
          <Tocapu motif={meta.motif} dye={meta.dye} size={16} />
          {meta.singular}
        </p>
        <h3 className="text-lg font-bold leading-snug">
          <Link to={href} className="hover:text-primary">
            {titleOf(item as never)}
          </Link>
        </h3>
        <CardMeta item={item} type={type} />
      </div>
    </article>
  )
}

function CardFlag({ item, type }: { item: AnyContent; type: ContentType }) {
  if (type === 'promociones') {
    const p = item as Promocion
    const text =
      p.discount_type === '2x1' ? '2 × 1' : p.discount_type === 'percentage' ? `-${p.discount_percentage ?? 0}%` : `-${formatPrice(p.discount_amount) ?? ''}`
    return <span className="absolute left-3 top-3 rounded-full bg-sun px-3 py-1 font-display text-lg font-extrabold text-ink">{text}</span>
  }
  if (type === 'eventos') {
    const e = item as Evento
    const d = new Date(e.start_time)
    return (
      <span className="absolute left-3 top-3 flex flex-col items-center rounded-xl bg-white px-3 py-1.5 leading-none text-ink">
        <span className="font-display text-2xl font-extrabold">{d.getDate()}</span>
        <span className="text-xs font-semibold">{d.toLocaleDateString('es-PE', { month: 'short' })}</span>
      </span>
    )
  }
  return null
}

function CardMeta({ item, type }: { item: AnyContent; type: ContentType }) {
  const row = 'flex items-center gap-1.5 text-sm text-ink-soft'
  switch (type) {
    case 'experiencias': {
      const e = item as Experiencia
      return (
        <>
          <p className={row}>
            {e.duration_hours ? (
              <>
                <Clock className="size-4" aria-hidden /> {e.duration_hours} h ·{' '}
              </>
            ) : null}
            Dificultad {DIFFICULTY[e.difficulty]?.toLowerCase()}
          </p>
          {e.anfitrion && (
            <p className="flex items-center gap-2 text-sm">
              {e.anfitrion.photo_url ? (
                <img src={e.anfitrion.photo_url} alt="" className="size-6 rounded-full object-cover" />
              ) : (
                <Tocapu motif="persona" dye="cochinilla" size={18} />
              )}
              Te recibe <strong className="font-semibold">{e.anfitrion.name}</strong>
              {e.anfitrion.audio_url && <span className="text-xs text-cochinilla">· con saludo</span>}
            </p>
          )}
          <p className="mt-auto pt-1 font-display text-lg font-bold text-primary">{formatPrice(e.price_pen) ?? 'Consultar precio'}</p>
        </>
      )
    }
    case 'eventos': {
      const e = item as Evento
      return (
        <>
          <p className={row}>
            <CalendarDays className="size-4" aria-hidden /> {formatShortDate(e.start_time)}, {formatTime(e.start_time)} · {EVENT_CATEGORIES[e.category] ?? e.category}
          </p>
          {e.short_address && (
            <p className={row}>
              <MapPin className="size-4" aria-hidden /> {e.short_address}
            </p>
          )}
        </>
      )
    }
    case 'promociones': {
      const p = item as Promocion
      return (
        <>
          {p.locale && <p className={row}>En {p.locale.name}</p>}
          <p className={row}>Válida hasta el {formatShortDate(p.end_date)}</p>
        </>
      )
    }
    case 'locales': {
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
    default:
      return <p className="line-clamp-2 text-sm text-ink-soft">{item.description}</p>
  }
}
