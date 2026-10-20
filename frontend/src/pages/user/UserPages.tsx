import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { CalendarDays, Compass, Heart, Star, Ticket } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { Link } from 'react-router'
import { useAuth } from '../../auth/AuthContext'
import { ContentCard } from '../../components/ContentCard'
import { CouponCard } from '../../components/CouponCard'
import { SaveForTripButton } from '../../components/OfflineTools'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Button, ButtonLink } from '../../components/ui/Button'
import { Alert, Badge, EmptyState, ErrorState, Pagination, Spinner, Stars, StatusBadge } from '../../components/ui/Feedback'
import { TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError, type Paginated } from '../../lib/api'
import { CONTENT, typeFromModelClass } from '../../lib/content'
import { formatDate, formatPrice, formatTime } from '../../lib/format'
import { useDocumentTitle } from '../../lib/hooks'
import type { AnyContent, Booking, EventAttendance, Favorite, Review, User, UserCoupon } from '../../lib/types'

function usePaged<T>(key: string, url: string, page: number) {
  return useQuery({ queryKey: [key, page], queryFn: () => getData<Paginated<T>>(url, { page }) })
}

/* ---------- Resumen ---------- */

export function AccountHome() {
  useDocumentTitle('Mi cuenta')
  const { user } = useAuth()
  const { data } = useQuery({ queryKey: ['profile'], queryFn: () => getData<User>('/profile') })
  const cards = [
    { to: '/cuenta/favoritos', label: 'Favoritos', value: data?.favorites_count, icon: Heart },
    { to: '/cuenta/reservas', label: 'Reservas', value: data?.bookings_count, icon: Compass },
    { to: '/cuenta/eventos', label: 'Asistencias', value: data?.event_attendances_count, icon: CalendarDays },
    { to: '/cuenta/cupones', label: 'Cupones', value: data?.coupons_count, icon: Ticket },
    { to: '/cuenta/resenas', label: 'Reseñas', value: data?.reviews_count, icon: Star },
  ]
  return (
    <>
      <PageHeader title={`Hola, ${user?.name.split(' ')[0]}`} description="Todo lo que guardaste y reservaste en Turiscovery." />
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
        {cards.map((c) => (
          <Link key={c.to} to={c.to} className="flex flex-col gap-3 rounded-[var(--radius-card)] bg-white p-5 transition-colors hover:bg-primary/5">
            <c.icon className="size-5 text-primary" aria-hidden />
            <span className="font-display text-4xl font-extrabold">{c.value ?? '–'}</span>
            <span className="text-sm text-ink-soft">{c.label}</span>
          </Link>
        ))}
      </div>
      {(user?.role === 'socio' || user?.role === 'admin') && (
        <Alert tone="info">
          Tienes una cuenta de {user.role === 'admin' ? 'administrador' : 'socio'}. Gestiona tu contenido desde el{' '}
          <Link to={user.role === 'admin' ? '/admin' : '/dashboard'} className="font-semibold text-primary">panel</Link>.
        </Alert>
      )}
      <section className="mt-10 flex flex-col gap-3 rounded-[var(--radius-card)] bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-lg font-bold">¿Vas al lago o a las islas?</h2>
          <p className="text-sm text-ink-soft">Allí casi no hay señal. Guarda tus cupones, reservas y tu plan antes de salir.</p>
        </div>
        <SaveForTripButton variant="primary" />
      </section>
      <div className="mt-10 flex flex-wrap gap-3">
        <ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>
        <ButtonLink to="/eventos" variant="secondary">Ver próximos eventos</ButtonLink>
      </div>
    </>
  )
}

/* ---------- Favoritos ---------- */

export function FavoritesPage() {
  useDocumentTitle('Favoritos')
  const [page, setPage] = useState(1)
  const { data, isLoading, isError, error, refetch } = usePaged<Favorite>('favorites', '/favorites', page)
  const items = (data?.data ?? []).filter((f) => f.favoritable && typeFromModelClass(f.favoritable_type))

  return (
    <>
      <PageHeader title="Favoritos" description="Lo que guardaste para no perderlo de vista." />
      {isLoading ? (
        <Spinner />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : items.length ? (
        <>
          <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 xl:grid-cols-3">
            {items.map((f) => (
              <ContentCard key={f.id} item={f.favoritable as AnyContent} type={typeFromModelClass(f.favoritable_type)!} />
            ))}
          </div>
          <Pagination page={data!.current_page} lastPage={data!.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState title="Aún no guardas nada" icon={<Heart className="size-8" />} action={<ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>}>
          Toca el corazón en cualquier experiencia, evento, promoción o local para guardarlo aquí.
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Reservas ---------- */

export function BookingsPage() {
  useDocumentTitle('Reservas')
  const [page, setPage] = useState(1)
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = usePaged<Booking>('bookings', '/bookings', page)
  const cancel = useMutation({
    mutationFn: (id: number) => api.post(`/bookings/${id}/cancel`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['bookings'] })
      notify('Reserva cancelada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader title="Reservas" description="El socio confirma cada reserva y te contacta con los detalles." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="grid gap-3 lg:grid-cols-2">
          {data.data.map((b) => {
            const future = new Date(b.booking_date) > new Date()
            return (
              <article key={b.id} className="card flex flex-col gap-2 p-4">
                <div className="flex items-start justify-between gap-3">
                  <StatusBadge status={b.status} />
                  <span className="font-display text-lg font-extrabold">{Number(b.total_price) > 0 ? formatPrice(b.total_price) : ''}</span>
                </div>
                <Link to={`/experiencias/${b.experiencia_id}`} className="t-h3 hover:text-primary-dark">{b.experiencia?.title ?? 'Experiencia'}</Link>
                <p className="text-sm text-ink-soft first-letter:uppercase">
                  {formatDate(b.booking_date)} · {b.participants} persona{b.participants > 1 ? 's' : ''}
                </p>
                <div className="flex flex-wrap items-center justify-between gap-3 border-t-2 border-line pt-3 text-sm">
                  <span className="text-ink-soft">
                    {b.status === 'pending' && 'Esperando que el socio la confirme'}
                    {b.status === 'confirmed' && 'Confirmada · muéstrala al llegar'}
                    {b.status === 'cancelled' && 'Cancelada · sin costo'}
                    {b.status === 'completed' && (
                      <Link to={`/experiencias/${b.experiencia_id}#resenas`} className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Cuéntanos cómo te fue</Link>
                    )}
                  </span>
                  {['pending', 'confirmed'].includes(b.status) && future && (
                    <Button size="sm" variant="ghost" loading={cancel.isPending && cancel.variables === b.id} loadingText="Cancelando…" onClick={() => confirm('¿Cancelar esta reserva?') && cancel.mutate(b.id)}>
                      Cancelar reserva
                    </Button>
                  )}
                </div>
              </article>
            )
          })}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="No tienes reservas" icon={<Compass className="size-8" />} action={<ButtonLink to="/experiencias">Ver experiencias</ButtonLink>}>
          Reserva un tour o taller desde la página de cualquier experiencia.
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Asistencias ---------- */

export function AttendancesPage() {
  useDocumentTitle('Asistencias')
  const [page, setPage] = useState(1)
  const { data, isLoading } = usePaged<EventAttendance>('attendances', '/event-attendances', page)
  return (
    <>
      <PageHeader title="Asistencias a eventos" description="Eventos en los que confirmaste que irás o marcaste que quizás vayas." />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="flex flex-col gap-3">
          {data.data.map((a) => (
            <article key={a.id} className="flex flex-wrap items-center gap-4 rounded-[var(--radius-card)] bg-white p-5">
              <div className="min-w-0 flex-1">
                <Link to={`/eventos/${a.evento_id}`} className="font-bold hover:text-primary">{a.evento?.title ?? 'Evento'}</Link>
                {a.evento && <p className="text-sm text-ink-soft">{formatDate(a.evento.start_time)}, {formatTime(a.evento.start_time)}</p>}
              </div>
              <StatusBadge status={a.status === 'confirmed' ? 'confirmed' : 'maybe'} label={a.status === 'confirmed' ? 'Voy' : 'Quizás'} />
            </article>
          ))}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="Sin eventos confirmados" icon={<CalendarDays className="size-8" />} action={<ButtonLink to="/eventos">Ver eventos</ButtonLink>}>
          Confirma tu asistencia desde la página de un evento.
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Cupones ---------- */

export function CouponsPage() {
  useDocumentTitle('Cupones')
  const [page, setPage] = useState(1)
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = usePaged<UserCoupon>('coupons', '/user-coupons', page)
  const redeem = useMutation({
    mutationFn: (id: number) => api.post(`/user-coupons/${id}/redeem`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['coupons'] })
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
        <EmptyState title="No tienes cupones" icon={<Ticket className="size-8" />} action={<ButtonLink to="/promociones">Ver promociones</ButtonLink>}>
          Obtén un cupón desde cualquier promoción vigente.
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Reseñas ---------- */

export function MyReviewsPage() {
  useDocumentTitle('Mis reseñas')
  const [page, setPage] = useState(1)
  const { data, isLoading } = usePaged<Review>('my-reviews', '/my-reviews', page)
  return (
    <>
      <PageHeader title="Mis reseñas" />
      {isLoading ? (
        <Spinner />
      ) : data?.data.length ? (
        <div className="flex flex-col gap-3">
          {data.data.map((r) => {
            const type = typeFromModelClass(r.reviewable_type)
            const name = r.reviewable?.title ?? r.reviewable?.name ?? 'Contenido retirado'
            return (
              <article key={r.id} className="rounded-[var(--radius-card)] bg-white p-5">
                <p className="flex flex-wrap items-center gap-2 text-sm text-ink-soft">
                  <Stars value={r.rating} /> {formatDate(r.created_at)}
                  {type && <Badge>{CONTENT[type].singular}</Badge>}
                </p>
                {type && r.reviewable ? (
                  <Link to={`${CONTENT[type].path}/${r.reviewable_id}`} className="mt-2 block font-bold hover:text-primary">{name}</Link>
                ) : (
                  <p className="mt-2 font-bold">{name}</p>
                )}
                {r.title && <p className="mt-1 font-semibold">{r.title}</p>}
                {r.comment && <p className="text-ink-soft">{r.comment}</p>}
              </article>
            )
          })}
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </div>
      ) : (
        <EmptyState title="Aún no escribes reseñas" icon={<Star className="size-8" />}>
          Después de una experiencia o evento, cuenta cómo te fue desde su página.
        </EmptyState>
      )}
    </>
  )
}

/* ---------- Perfil ---------- */

export function ProfilePage() {
  useDocumentTitle('Perfil')
  const { refresh } = useAuth()
  const notify = useToast()
  const qc = useQueryClient()
  const { data: profile, isLoading } = useQuery({ queryKey: ['profile'], queryFn: () => getData<User>('/profile') })

  if (isLoading || !profile) return <Spinner />
  return (
    <>
      <PageHeader title="Perfil" description={profile.email} />
      <ProfileForm
        profile={profile}
        onSaved={() => {
          void qc.invalidateQueries({ queryKey: ['profile'] })
          void refresh()
          notify('Perfil actualizado')
        }}
      />
    </>
  )
}

function ProfileForm({ profile, onSaved }: { profile: User; onSaved: () => void }) {
  const [form, setForm] = useState({
    name: profile.name,
    phone: profile.phone ?? '',
    bio: profile.bio ?? '',
    current_password: '',
    password: '',
    password_confirmation: '',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const save = useMutation({
    mutationFn: () => {
      const body: Record<string, string> = { name: form.name, phone: form.phone, bio: form.bio }
      if (form.password) Object.assign(body, { current_password: form.current_password, password: form.password, password_confirmation: form.password_confirmation })
      return api.put('/profile', body)
    },
    onSuccess: () => {
      setErrors({})
      setForm((f) => ({ ...f, current_password: '', password: '', password_confirmation: '' }))
      onSaved()
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message })
    },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))
  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    save.mutate()
  }

  return (
    <form onSubmit={onSubmit} className="flex max-w-2xl flex-col gap-8">
      <fieldset className="flex flex-col gap-4 rounded-[var(--radius-card)] bg-white p-6">
        <legend className="mb-2 text-lg font-bold">Datos personales</legend>
        <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required />
        <TextField label="Teléfono" type="tel" value={form.phone} onChange={set('phone')} error={errors.phone} />
        <TextArea label="Sobre ti" value={form.bio} onChange={set('bio')} error={errors.bio} maxLength={1000} />
      </fieldset>
      <fieldset className="flex flex-col gap-4 rounded-[var(--radius-card)] bg-white p-6">
        <legend className="mb-2 text-lg font-bold">Cambiar contraseña</legend>
        <p className="text-sm text-ink-soft">Déjalo en blanco si no quieres cambiarla.</p>
        <TextField label="Contraseña actual" type="password" autoComplete="current-password" value={form.current_password} onChange={set('current_password')} error={errors.current_password} />
        <TextField label="Nueva contraseña" type="password" autoComplete="new-password" value={form.password} onChange={set('password')} error={errors.password} hint="Mínimo 8 caracteres con mayúsculas, minúsculas, un número y un símbolo." />
        <TextField label="Repite la nueva contraseña" type="password" autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} />
      </fieldset>
      {errors._ && <Alert tone="error">{errors._}</Alert>}
      <div>
        <Button type="submit" loading={save.isPending}>Guardar cambios</Button>
      </div>
    </form>
  )
}
