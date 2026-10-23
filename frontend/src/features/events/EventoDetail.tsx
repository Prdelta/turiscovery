import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { CalendarDays, CalendarPlus } from 'lucide-react'
import { useState } from 'react'
import { useAuth } from '@/shared/auth/AuthContext'
import { Button } from '@/shared/ui/Button'
import { Alert, Badge } from '@/shared/ui/Feedback'
import { TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { EVENT_CATEGORIES } from '@/shared/config/content'
import { daysUntil, formatDate, formatPrice, formatTime, googleCalendarUrl, relativeDays } from '@/shared/lib/format'
import type { Evento } from '@/shared/api/types'
import { accountKeys } from '@/features/account/api'
import { eventKeys, eventQueries } from '@/features/events/api'
import { AsideCard, DetailShell, DetailState, LocaleLink, LoginPrompt, useDetail } from '@/features/catalog/detail/DetailShell'

export function EventoDetail() {
  const { data, isLoading, error, refetch } = useDetail<Evento>('eventos')
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const e = data
  const days = daysUntil(e.start_time)
  return (
    <DetailShell
      item={e}
      type="eventos"
      kicker={
        <>
          <Badge>{EVENT_CATEGORIES[e.category] ?? e.category}</Badge>
          {days >= 0 && <Badge className="border-2 border-mint-dark bg-mint/20 text-[#1f6b2f]">{relativeDays(e.start_time)}</Badge>}
        </>
      }
      meta={
        <>
          <p className="flex items-center gap-2">
            <CalendarDays className="size-4.5" aria-hidden /> {formatDate(e.start_time)}, {formatTime(e.start_time)} – {formatDate(e.end_time) === formatDate(e.start_time) ? formatTime(e.end_time) : `${formatDate(e.end_time)}, ${formatTime(e.end_time)}`}
          </p>
          <LocaleLink locale={e.locale} />
        </>
      }
      aside={<AttendanceCard evento={e} />}
    >
      {e.content && <div className="prose-measure mt-6 whitespace-pre-line text-ink-soft">{e.content}</div>}
    </DetailShell>
  )
}

function AttendanceCard({ evento }: { evento: Evento }) {
  const { user } = useAuth()
  const qc = useQueryClient()
  const notify = useToast()
  const [guests, setGuests] = useState(1)

  const count = useQuery(eventQueries.attendanceCount(evento.id))
  const mine = useQuery({ ...eventQueries.myAttendance(evento.id), enabled: !!user })

  const refresh = () => {
    void qc.invalidateQueries({ queryKey: eventKeys.attendance(evento.id) })
    void qc.invalidateQueries({ queryKey: accountKeys.all })
  }

  const attend = useMutation({
    mutationFn: (status: 'confirmed' | 'maybe') => api.post('/event-attendances', { evento_id: evento.id, status, guests }),
    onSuccess: () => {
      refresh()
      notify('Asistencia registrada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const cancel = useMutation({
    mutationFn: () => api.delete(`/event-attendances/${evento.id}`),
    onSuccess: () => {
      refresh()
      notify('Asistencia cancelada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  const price = formatPrice(evento.ticket_price)
  const attending = mine.data?.attending ? mine.data.data : null

  return (
    <AsideCard>
      <div>
        <p className="font-display text-4xl font-extrabold">{price ?? "Entrada libre"}</p>
        {count.data && (
          <p className="mt-1 text-sm text-ink-soft">
            {count.data.confirmed} confirmados{count.data.maybe ? ` · ${count.data.maybe} interesados` : ''}
          </p>
        )}
      </div>
      {!user ? (
        <LoginPrompt action="confirmar asistencia" />
      ) : attending ? (
        <>
          <Alert tone="success">
            {attending.status === 'confirmed' ? 'Confirmaste tu asistencia' : 'Marcaste que quizás vayas'}
            {attending.guests && attending.guests > 1 ? ` con ${attending.guests} personas` : ''}.
          </Alert>
          {attending.status === 'maybe' && (
            <Button onClick={() => attend.mutate('confirmed')} loading={attend.isPending}>Confirmar que voy</Button>
          )}
          <Button variant="secondary" onClick={() => cancel.mutate()} loading={cancel.isPending}>Cancelar asistencia</Button>
        </>
      ) : (
        <>
          <TextField label="¿Cuántos van?" type="number" min={1} max={10} value={guests} onChange={(e) => setGuests(Math.min(10, Math.max(1, Number(e.target.value))))} />
          <Button onClick={() => attend.mutate('confirmed')} loading={attend.isPending && attend.variables === 'confirmed'}>Voy a ir</Button>
          <Button variant="secondary" onClick={() => attend.mutate('maybe')} loading={attend.isPending && attend.variables === 'maybe'}>Quizás vaya</Button>
        </>
      )}
      <a
        href={googleCalendarUrl(evento.title, evento.start_time, evento.end_time, evento.description, evento.formatted_address || evento.address)}
        target="_blank"
        rel="noreferrer"
        className="inline-flex items-center gap-2 font-bold text-primary-dark underline decoration-2 underline-offset-4"
      >
        <CalendarPlus className="size-4.5" /> Añadir a Google Calendar
      </a>
    </AsideCard>
  )
}
