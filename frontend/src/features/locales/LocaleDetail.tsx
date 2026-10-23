import { Globe, Mail, MapPin, Phone } from 'lucide-react'
import { ContentCard } from '@/features/catalog/components/ContentCard'
import { Badge } from '@/shared/ui/Feedback'
import { CONTENT, LOCALE_CATEGORIES, type ContentType } from '@/shared/config/content'
import type { AnyContent, Evento, Experiencia, Locale, Promocion } from '@/shared/api/types'
import { AsideCard, DetailShell, DetailState, useDetail } from '@/features/catalog/detail/DetailShell'

export function LocaleDetail() {
  const { data, isLoading, error, refetch } = useDetail<Locale & { experiencias?: Experiencia[]; eventos?: Evento[]; promociones?: Promocion[] }>('locales')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const l = data
  const related: [ContentType, AnyContent[] | undefined][] = [
    ['promociones', l.promociones],
    ['experiencias', l.experiencias],
    ['eventos', l.eventos],
  ]
  return (
    <DetailShell
      item={l}
      type="locales"
      kicker={<Badge>{LOCALE_CATEGORIES[l.category] ?? l.category}</Badge>}
      aside={
        <AsideCard>
          <h2 className="t-h3">Contacto</h2>
          <ul className="flex flex-col gap-3">
            {l.phone && <li><a href={`tel:${l.phone}`} className="flex items-center gap-2 hover:text-primary"><Phone className="size-4.5" /> {l.phone}</a></li>}
            {l.email && <li><a href={`mailto:${l.email}`} className="flex items-center gap-2 break-all hover:text-primary"><Mail className="size-4.5 shrink-0" /> {l.email}</a></li>}
            {l.website && <li><a href={l.website} target="_blank" rel="noreferrer" className="flex items-center gap-2 break-all hover:text-primary"><Globe className="size-4.5 shrink-0" /> {l.website.replace(/^https?:\/\//, '')}</a></li>}
            {(l.formatted_address || l.address) && <li className="flex items-start gap-2"><MapPin className="mt-1 size-4.5 shrink-0" /> {l.formatted_address || l.address}</li>}
          </ul>
          {!l.phone && !l.email && !l.website && <p className="text-ink-soft">Este local aún no publicó sus datos de contacto.</p>}
        </AsideCard>
      }
    >
      {related.map(([type, items]) =>
        items?.length ? (
          <section key={type} className="mt-12">
            <h2 className="mb-5 t-h2">{CONTENT[type].plural} de este local</h2>
            <div className="grid gap-x-6 gap-y-8 sm:grid-cols-2">
              {items.map((it) => <ContentCard key={it.id} item={it} type={type} />)}
            </div>
          </section>
        ) : null,
      )}
    </DetailShell>
  )
}
