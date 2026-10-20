import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Heart } from 'lucide-react'
import { useNavigate } from 'react-router'
import { useAuth } from '../auth/AuthContext'
import { api, parseApiError } from '../lib/api'
import { CONTENT, type ContentType } from '../lib/content'
import { useToast } from './ui/Toast'

export function FavoriteButton({ type, id, compact = false }: { type: ContentType; id: number; compact?: boolean }) {
  const { user } = useAuth()
  const navigate = useNavigate()
  const notify = useToast()
  const qc = useQueryClient()
  const modelClass = CONTENT[type].modelClass
  const key = ['favorite', modelClass, id]

  const { data: isFavorited = false } = useQuery({
    queryKey: key,
    enabled: !!user,
    staleTime: 60_000,
    queryFn: async () => {
      const res = await api.get<{ is_favorited: boolean }>('/favorites/check', {
        params: { favoritable_type: modelClass, favoritable_id: id },
      })
      return res.data.is_favorited
    },
  })

  const toggle = useMutation({
    mutationFn: async () => {
      const res = await api.post<{ is_favorited: boolean }>('/favorites/toggle', { favoritable_type: modelClass, favoritable_id: id })
      return res.data.is_favorited
    },
    onSuccess: (value) => {
      qc.setQueryData(key, value)
      void qc.invalidateQueries({ queryKey: ['favorites'] })
      notify(value ? 'Guardado en tus favoritos' : 'Quitado de tus favoritos')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  const onClick = () => {
    if (!user) {
      navigate(`/login?next=${encodeURIComponent(location.pathname)}`)
      return
    }
    toggle.mutate()
  }

  const label = isFavorited ? 'Quitar de favoritos' : 'Guardar en favoritos'

  // Sin animación de "latido": el cambio de relleno basta para mostrar el estado
  if (compact) {
    return (
      <button
        onClick={onClick}
        aria-label={label}
        aria-pressed={isFavorited}
        disabled={toggle.isPending}
        className={clsx(
          'inline-flex size-11 items-center justify-center rounded-[var(--radius-control)] border-2 transition-colors',
          isFavorited ? 'border-cochinilla bg-cochinilla text-white' : 'border-ink bg-white text-ink hover:bg-sky',
        )}
      >
        <Heart className={clsx('size-5', isFavorited && 'fill-white')} />
      </button>
    )
  }

  return (
    <button
      onClick={onClick}
      aria-pressed={isFavorited}
      disabled={toggle.isPending}
      className={clsx(
        'inline-flex min-h-11 items-center gap-2 rounded-[var(--radius-control)] border-2 px-4 font-bold transition-colors',
        isFavorited ? 'border-cochinilla bg-cochinilla text-white' : 'border-ink bg-white text-ink hover:bg-sky',
      )}
    >
      <Heart className={clsx('size-5', isFavorited && 'fill-white')} />
      {isFavorited ? 'En favoritos' : 'Guardar'}
    </button>
  )
}
