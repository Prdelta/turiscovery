import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { MessageSquare, Trash2 } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { Link } from 'react-router'
import { useAuth } from '../auth/AuthContext'
import { api, getData, parseApiError, type Paginated } from '../lib/api'
import { CONTENT, type ContentType } from '../lib/content'
import { formatDate } from '../lib/format'
import type { Review } from '../lib/types'
import { Avatar } from './layout/SiteLayout'
import { Button } from './ui/Button'
import { Alert, Stars, StarInput } from './ui/Feedback'
import { TextArea, TextField } from './ui/Field'
import { useToast } from './ui/Toast'

export function ReviewsSection({ type, id }: { type: ContentType; id: number }) {
  const { user } = useAuth()
  const modelClass = CONTENT[type].modelClass
  const key = ['reviews', modelClass, id]

  const { data, isLoading } = useQuery({
    queryKey: key,
    queryFn: () => getData<Paginated<Review>>('/reviews', { reviewable_type: modelClass, reviewable_id: id }),
  })

  const reviews = data?.data ?? []
  const average = reviews.length ? reviews.reduce((s, r) => s + r.rating, 0) / reviews.length : 0
  const mine = user ? reviews.find((r) => r.user_id === user.id) : undefined

  return (
    <section aria-labelledby="resenas" className="mt-16">
      <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 id="resenas" className="text-2xl font-extrabold">Reseñas</h2>
          {reviews.length > 0 && (
            <p className="mt-1 flex items-center gap-2 text-ink-soft">
              <Stars value={average} /> {average.toFixed(1)} · {data?.total ?? reviews.length} opinión{reviews.length === 1 ? '' : 'es'}
            </p>
          )}
        </div>
      </div>

      <div className="grid gap-8 lg:grid-cols-[1fr_380px]">
        <div className="flex flex-col gap-4">
          {isLoading && <div className="skeleton h-28" />}
          {!isLoading && reviews.length === 0 && (
            <p className="flex items-center gap-2 text-ink-soft">
              <MessageSquare className="size-5" aria-hidden /> Aún nadie ha opinado. Cuenta cómo te fue.
            </p>
          )}
          {reviews.map((r) => (
            <ReviewItem key={r.id} review={r} canDelete={!!user && (user.id === r.user_id || user.role === 'admin')} queryKey={key} />
          ))}
        </div>

        <div className="rounded-[var(--radius-card)] bg-white p-5">
          {user ? (
            <ReviewForm modelClass={modelClass} id={id} existing={mine} queryKey={key} />
          ) : (
            <div className="flex flex-col gap-3">
              <h3 className="text-lg font-bold">¿Ya lo visitaste?</h3>
              <p className="text-ink-soft">Inicia sesión para dejar tu reseña y ayudar a otros viajeros.</p>
              <Link to={`/login?next=${encodeURIComponent(location.pathname)}`} className="font-semibold text-primary">
                Iniciar sesión para opinar
              </Link>
            </div>
          )}
        </div>
      </div>
    </section>
  )
}

function ReviewItem({ review, canDelete, queryKey }: { review: Review; canDelete: boolean; queryKey: unknown[] }) {
  const qc = useQueryClient()
  const notify = useToast()
  const remove = useMutation({
    mutationFn: () => api.delete(`/reviews/${review.id}`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey })
      notify('Reseña eliminada')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <article className="rounded-[var(--radius-card)] bg-white p-5">
      <header className="flex items-start gap-3">
        <Avatar name={review.user?.name ?? 'Viajero'} src={review.user?.avatar} />
        <div className="flex-1">
          <p className="font-semibold">{review.user?.name ?? 'Viajero'}</p>
          <p className="flex items-center gap-2 text-sm text-ink-soft">
            <Stars value={review.rating} /> {formatDate(review.created_at)}
          </p>
        </div>
        {canDelete && (
          <button
            onClick={() => confirm('¿Eliminar esta reseña?') && remove.mutate()}
            className="rounded-full p-2 text-ink-soft hover:bg-danger/10 hover:text-danger"
            aria-label="Eliminar reseña"
          >
            <Trash2 className="size-4" />
          </button>
        )}
      </header>
      {review.title && <h3 className="mt-3 font-bold">{review.title}</h3>}
      {review.comment && <p className="mt-1 text-ink-soft">{review.comment}</p>}
    </article>
  )
}

function ReviewForm({ modelClass, id, existing, queryKey }: { modelClass: string; id: number; existing?: Review; queryKey: unknown[] }) {
  const qc = useQueryClient()
  const notify = useToast()
  const [rating, setRating] = useState(existing?.rating ?? 0)
  const [title, setTitle] = useState(existing?.title ?? '')
  const [comment, setComment] = useState(existing?.comment ?? '')
  const [error, setError] = useState<string | null>(null)

  const save = useMutation({
    mutationFn: async () => {
      if (existing) await api.put(`/reviews/${existing.id}`, { rating, title, comment })
      else await api.post('/reviews', { reviewable_type: modelClass, reviewable_id: id, rating, title, comment })
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey })
      notify(existing ? 'Reseña actualizada' : 'Reseña publicada')
      setError(null)
    },
    onError: (e) => setError(parseApiError(e).message),
  })

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    if (!rating) {
      setError('Elige de 1 a 5 estrellas.')
      return
    }
    save.mutate()
  }

  return (
    <form onSubmit={onSubmit} className="flex flex-col gap-4">
      <h3 className="text-lg font-bold">{existing ? 'Tu reseña' : 'Escribe una reseña'}</h3>
      <StarInput value={rating} onChange={setRating} />
      <TextField label="Título" value={title} onChange={(e) => setTitle(e.target.value)} maxLength={255} placeholder="Resume tu experiencia" />
      <TextArea label="Comentario" value={comment} onChange={(e) => setComment(e.target.value)} maxLength={1000} placeholder="¿Qué te gustó? ¿Qué mejorarías?" />
      {error && <Alert tone="error">{error}</Alert>}
      <Button type="submit" loading={save.isPending}>
        {existing ? 'Guardar cambios' : 'Publicar reseña'}
      </Button>
    </form>
  )
}
