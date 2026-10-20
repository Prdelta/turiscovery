import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ChevronLeft, Clock, MapPin, Minus, Plus } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { Link, useNavigate, useParams, useSearchParams } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { SaveForTripButton } from '@/features/trip/SaveForTripButton'
import { TypeTag } from '@/shared/textile/Tags'
import { ProgressBand, StitchRow } from '@/shared/textile/Weave'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { Alert, ErrorState, Spinner, StatePanel } from '@/shared/ui/Feedback'
import { TextArea, TextField } from '@/shared/ui/Field'
import { altitudeWarningFor } from '@/features/planner/altitude'
import { parseApiError } from '@/shared/api/client'
import { imageOf } from '@/shared/config/content'
import { formatPrice, toDateInput } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { useContentItem } from '@/features/catalog/api'
import { useOnline } from '@/shared/offline/offline'
import type { Experiencia } from '@/shared/api/types'
import { bookingQueries, useCreateBooking } from '@/features/bookings/api'

/** Fecha "YYYY-MM-DD" como fecha local (sin desfase por zona horaria) */
function localDate(iso: string) {
  const [y, m, d] = iso.split('-').map(Number)
  return new Date(y, m - 1, d)
}

/**
 * Reserva de una experiencia en una sola pantalla (guía Telar):
 * día con hora de salida y cupos, número de personas, aviso de altura y total fijo abajo.
 */
export default function BookingPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { user, loading: authLoading } = useAuth()
  const online = useOnline()
  const [params] = useSearchParams()
  const experiencia = useContentItem<Experiencia>('experiencias', id)
  useDocumentTitle(experiencia.data ? `Reservar: ${experiencia.data.title}` : 'Reservar')

  const availability = useQuery(bookingQueries.availability(id))

  const days = availability.data ?? []
  const fromPlan = params.get('fecha')
  const tomorrow = toDateInput(new Date(Date.now() + 86_400_000).toISOString())
  const [picked, setPicked] = useState<string | null>(null)
  // Sin elección propia: la fecha que viene del planificador, o el primer día con cupos
  const date =
    picked ??
    (fromPlan && fromPlan >= tomorrow ? fromPlan : null) ??
    days.find((d) => d.available === null || d.available === undefined || d.available > 0)?.date ??
    null
  const day = days.find((d) => d.date === date)

  const [participants, setParticipants] = useState(1)
  const [phone, setPhone] = useState(user?.phone ?? '')
  const [notes, setNotes] = useState('')
  const [error, setError] = useState<string | null>(null)

  const createBooking = useCreateBooking()
  const book = {
    ...createBooking,
    mutate: (count: number) =>
      createBooking.mutate(
        { experiencia_id: Number(id), booking_date: date!, participants: count, contact_phone: phone || undefined, notes: notes || undefined },
        { onSuccess: () => setError(null), onError: (e) => setError(parseApiError(e).message) },
      ),
  }

  if (experiencia.isLoading || authLoading) return <Spinner />
  if (!experiencia.data) {
    return (
      <div className="container-page py-16">
        <ErrorState message={parseApiError(experiencia.error).message} onRetry={() => experiencia.refetch()} />
      </div>
    )
  }

  const e = experiencia.data
  const price = Number(e.price_pen ?? 0)
  const maxPeople = Math.max(1, Math.min(day?.available ?? Infinity, e.max_participants ?? 20))
  const people = Math.min(participants, maxPeople)
  const altitudeWarning = date ? altitudeWarningFor(e.difficulty, date) : null
  const back = () => (window.history.length > 1 ? navigate(-1) : navigate(`/experiencias/${e.id}`))

  if (!user) {
    return (
      <div className="container-page max-w-2xl py-12">
        <StatePanel
          kind="empty"
          title="Inicia sesión para reservar"
          action={<ButtonLink to={`/login?next=${encodeURIComponent(location.pathname)}`}>Iniciar sesión</ButtonLink>}
        >
          Tu reserva queda en tu cuenta y la puedes ver sin conexión en el lago.
        </StatePanel>
      </div>
    )
  }

  if (book.isSuccess) {
    return (
      <div className="container-page max-w-2xl py-12">
        <ProgressBand total={3} done={3} dye="anil" size={20} label="Paso 3 de 3" />
        <StatePanel
          kind="success"
          title="¡Reserva enviada!"
          className="mt-6"
          action={
            <div className="flex flex-wrap justify-center gap-3">
              <ButtonLink to="/cuenta/reservas">Ver mis reservas</ButtonLink>
              <SaveForTripButton label="Guardar para el viaje" />
            </div>
          }
        >
          {e.title} · {date && localDate(date).toLocaleDateString('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })} · {people} persona{people > 1 ? 's' : ''}.
          El socio la confirma contigo; te avisamos en tu cuenta.
        </StatePanel>
      </div>
    )
  }

  const onSubmit = (ev: FormEvent) => {
    ev.preventDefault()
    if (!date) return setError('Elige un día para la salida.')
    setParticipants(people)
    book.mutate(people)
  }

  return (
    <form onSubmit={onSubmit} className="container-page max-w-2xl pb-40 pt-6 sm:pt-10 lg:pb-16">
      <div className="flex items-center gap-3">
        <button
          type="button"
          onClick={back}
          aria-label="Volver a la experiencia"
          className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white hover:bg-sky"
        >
          <ChevronLeft className="size-5" />
        </button>
        <div className="min-w-0 flex-1">
          <h1 className="t-h2">Reservar</h1>
          <ProgressBand total={3} done={date ? 2 : 1} dye="anil" size={14} label={`Paso ${date ? 2 : 1} de 3`} />
        </div>
      </div>

      {/* Resumen */}
      <Link to={`/experiencias/${e.id}`} className="card mt-6 flex gap-4 overflow-hidden p-3 hover:bg-sky">
        <img src={imageOf(e, 'experiencias')} alt="" className="size-24 shrink-0 rounded-[var(--radius-chip)] object-cover" />
        <span className="flex min-w-0 flex-col gap-1">
          <TypeTag type="experiencias" className="self-start" />
          <span className="t-h3 line-clamp-2">{e.title}</span>
          <span className="flex flex-wrap gap-x-3 text-sm text-ink-soft">
            {e.meeting_point && (
              <span className="inline-flex items-center gap-1"><MapPin className="size-4" aria-hidden /> {e.meeting_point}</span>
            )}
            {e.duration_hours ? (
              <span className="inline-flex items-center gap-1"><Clock className="size-4" aria-hidden /> {e.duration_hours} h</span>
            ) : null}
          </span>
        </span>
      </Link>

      {!online && (
        <div className="mt-6">
          <Alert tone="warning">Estás sin conexión. Puedes revisar los datos, pero reservar necesita señal.</Alert>
        </div>
      )}

      {/* Día */}
      <fieldset className="mt-8">
        <legend className="t-h3 mb-3">¿Qué día?</legend>
        {availability.isLoading ? (
          <div className="flex gap-3 overflow-hidden">
            {[0, 1, 2, 3].map((i) => <div key={i} className="skeleton h-32 w-28 shrink-0" />)}
          </div>
        ) : days.length ? (
          <div className="-mx-4 overflow-x-auto px-4 pb-2">
            <div className="flex w-max gap-3">
              {days.map((d) => {
                const full = d.available === 0
                const selected = d.date === date
                const dt = localDate(d.date)
                return (
                  <label
                    key={d.date}
                    className={clsx(
                      'flex w-28 shrink-0 cursor-pointer flex-col items-center gap-0.5 rounded-[var(--radius-card)] border-2 p-3 text-center transition-colors has-[:focus-visible]:outline-3 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-primary',
                      full ? 'cursor-not-allowed border-dashed border-line bg-lana text-ink-soft' : selected ? 'border-ink bg-ink text-white' : 'border-ink bg-white hover:bg-sky',
                    )}
                  >
                    <input type="radio" name="fecha" value={d.date} checked={selected} disabled={full} onChange={() => setPicked(d.date)} className="sr-only" />
                    <span className="t-label">{dt.toLocaleDateString('es-PE', { weekday: 'short' }).replace('.', '')}</span>
                    <span className="font-display text-3xl font-extrabold leading-none">{dt.getDate()}</span>
                    <span className="text-xs font-bold">{dt.toLocaleDateString('es-PE', { month: 'short' }).replace('.', '')}</span>
                    {d.departure_time && <span className="mt-1 text-sm">{d.departure_time} h</span>}
                    <span className={clsx('text-xs font-bold', !full && !selected && d.available != null && d.available <= 3 && 'text-cochinilla')}>
                      {full ? 'Sin cupos' : d.available == null ? 'Con cupos' : `${d.available} cupo${d.available === 1 ? '' : 's'}`}
                    </span>
                  </label>
                )
              })}
            </div>
          </div>
        ) : (
          // Sin disponibilidad (error o sin conexión): fecha libre, el socio la confirma
          <TextField label="Fecha" type="date" min={tomorrow} value={date ?? ''} onChange={(ev) => setPicked(ev.target.value)} required />
        )}
      </fieldset>

      {altitudeWarning && (
        <div className="mt-4">
          <Alert tone="warning">
            {altitudeWarning}{' '}
            <Link to="/planificador" className="font-bold underline decoration-2 underline-offset-4">Ver mi plan</Link>
          </Alert>
        </div>
      )}

      <StitchRow dye="anil" className="mt-8" />

      {/* Personas */}
      <fieldset className="mt-8">
        <legend className="t-h3 mb-3">¿Cuántas personas?</legend>
        <div className="card flex items-center justify-between gap-4 p-3 pl-5">
          <p>
            <span className="font-bold">{people} persona{people > 1 ? 's' : ''}</span>
            {price > 0 && <span className="block text-sm text-ink-soft">{formatPrice(price)} por persona</span>}
          </p>
          <div className="flex items-center gap-3">
            <Button type="button" variant="secondary" iconOnly aria-label="Una persona menos" disabled={people <= 1} onClick={() => setParticipants(people - 1)}>
              <Minus className="size-5" />
            </Button>
            <output aria-live="polite" className="w-8 text-center font-display text-2xl font-extrabold">{people}</output>
            <Button type="button" variant="secondary" iconOnly aria-label="Una persona más" disabled={people >= maxPeople} onClick={() => setParticipants(people + 1)}>
              <Plus className="size-5" />
            </Button>
          </div>
        </div>
        {day?.available != null && people >= maxPeople && <p className="mt-2 text-sm text-ink-soft">Ese día quedan {day.available} cupos.</p>}
      </fieldset>

      <div className="mt-8 grid gap-4">
        <TextField label="Teléfono de contacto" type="tel" value={phone} onChange={(ev) => setPhone(ev.target.value)} placeholder="+51 9…" />
        <TextArea label="Notas para el guía (opcional)" value={notes} onChange={(ev) => setNotes(ev.target.value)} maxLength={500} className="min-h-20" />
      </div>

      <div className="mt-6 flex flex-col gap-3">
        <Alert tone="info">No se cobra nada ahora. Pagas al socio el día de la salida, después de que confirme tu reserva.</Alert>
        {error && <Alert tone="error">{error}</Alert>}
      </div>

      {/* Total fijo: sobre la barra inferior en móvil, al pie del formulario en escritorio */}
      <div className="fixed inset-x-0 bottom-0 z-[1150] border-t-2 border-ink bg-white pb-[env(safe-area-inset-bottom)] lg:sticky lg:bottom-4 lg:z-auto lg:mt-8 lg:rounded-[var(--radius-panel)] lg:border-2 lg:pb-0">
        <div className="mx-auto flex max-w-2xl items-center justify-between gap-4 px-4 py-3 lg:px-5">
          <p>
            <span className="t-label block text-ink-soft">Total</span>
            <span className="font-display text-2xl font-extrabold">{price > 0 ? formatPrice(price * people) : 'A consultar'}</span>
          </p>
          <Button type="submit" size="lg" loading={book.isPending} loadingText="Reservando…" disabled={!online || !date}>
            Confirmar reserva
          </Button>
        </div>
      </div>
    </form>
  )
}
