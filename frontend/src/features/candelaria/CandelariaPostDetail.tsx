import { CalendarDays } from 'lucide-react'
import { Badge } from '@/shared/ui/Feedback'
import { CANDELARIA_CATEGORIES } from '@/shared/config/content'
import { formatDate } from '@/shared/lib/format'
import type { Candelaria } from '@/shared/api/types'
import { DetailShell, DetailState, useDetail } from '@/features/catalog/detail/DetailShell'

export function CandelariaPostDetail() {
  const { data, isLoading, error, refetch } = useDetail<Candelaria>('candelaria')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  return (
    <DetailShell
      item={data}
      type="candelaria"
      showMap={false}
      kicker={<Badge>{CANDELARIA_CATEGORIES[data.category] ?? data.category}</Badge>}
      meta={data.event_date ? <p className="flex items-center gap-2"><CalendarDays className="size-4.5" aria-hidden /> {formatDate(data.event_date)}</p> : undefined}
    >
      {data.content && <div className="prose-measure mt-6 whitespace-pre-line text-ink-soft">{data.content}</div>}
    </DetailShell>
  )
}
