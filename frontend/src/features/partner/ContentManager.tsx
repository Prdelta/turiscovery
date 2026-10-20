import { useMutation, useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Eye, EyeOff, Pencil, Plus, Search, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { useParams } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { PageHeader } from '@/shared/ui/PageHeader'
import { ButtonLink } from '@/shared/ui/Button'
import { Tocapu } from '@/shared/textile/Weave'
import { CardGridSkeleton, EmptyState, ErrorState, Pagination, StatusBadge } from '@/shared/ui/Feedback'
import { IconAction } from '@/shared/ui/Panel'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { CONTENT, imageOf, titleOf, type ContentType } from '@/shared/config/content'
import { formatDate } from '@/shared/lib/format'
import { useDebounced, useDocumentTitle } from '@/shared/lib/hooks'
import type { AnyContent } from '@/shared/api/types'
import { catalogQueries } from '@/features/catalog/api'
import { useInvalidateContent } from '@/features/partner/api'
import { MANAGED } from '@/features/partner/managed'

export function ContentManager() {
  const { type } = useParams() as { type: ContentType }
  const meta = CONTENT[type]
  useDocumentTitle(`Mis ${meta.plural.toLowerCase()}`)
  const { user } = useAuth()
  const notify = useToast()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const debounced = useDebounced(search)

  const list = useQuery(catalogQueries.mine(type, { page, search: debounced, per_page: 15 }))

  const invalidateContent = useInvalidateContent()
  const invalidate = () => invalidateContent(type)

  const toggle = useMutation({
    mutationFn: (item: AnyContent) => api.put(`/${type}/${item.id}`, { is_active: !item.is_active }),
    onSuccess: (_, item) => {
      invalidate()
      notify(item.is_active ? 'Ocultado del sitio público' : 'Publicado en el sitio')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/${type}/${id}`),
    onSuccess: () => {
      invalidate()
      notify(`${meta.singular} eliminado`)
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (!MANAGED.includes(type)) return <EmptyState title="Sección no encontrada" />

  return (
    <>
      <PageHeader
        title={meta.plural}
        description={user?.role === 'admin' ? 'Contenido de todos los socios.' : undefined}
        action={<ButtonLink to={`/dashboard/${type}/nuevo`} icon={<Plus className="size-4.5" />}>Nuevo</ButtonLink>}
      />
      <div className="relative mb-6 max-w-md">
        <label htmlFor="mine-search" className="sr-only">Buscar</label>
        <Search className="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-ink-soft" aria-hidden />
        <input id="mine-search" type="search" value={search} onChange={(e) => { setSearch(e.target.value); setPage(1) }} placeholder="Buscar por título" className="field-input pl-12" />
      </div>
      {list.isLoading ? (
        <CardGridSkeleton count={3} />
      ) : list.isError ? (
        <ErrorState message={parseApiError(list.error).message} onRetry={() => list.refetch()} />
      ) : list.data?.data.length ? (
        <>
          <ul className="flex flex-col gap-3">
            {list.data.data.map((item) => (
              <li key={item.id} className={clsx('card flex flex-wrap items-center gap-4 p-3 pr-4', !item.is_active && 'border-dashed')}>
                <img
                  src={imageOf(item, type)}
                  alt=""
                  className={clsx('size-16 rounded-[var(--radius-control)] border-2 border-ink object-cover', !item.is_active && 'grayscale')}
                />
                <div className="min-w-0 flex-1">
                  <p className="truncate font-bold">{titleOf(item as never)}</p>
                  <p className="text-sm text-ink-soft">
                    Creado el {formatDate(item.created_at)}
                    {user?.role === 'admin' && item.user ? ` · ${item.user.name}` : ''}
                  </p>
                </div>
                <StatusBadge status={item.is_active ? 'published' : 'hidden'} />
                <div className="flex gap-2">
                  <IconAction label={item.is_active ? 'Ocultar del sitio' : 'Publicar'} onClick={() => toggle.mutate(item)} disabled={toggle.isPending && toggle.variables?.id === item.id}>
                    {item.is_active ? <EyeOff className="size-4.5" /> : <Eye className="size-4.5" />}
                  </IconAction>
                  <IconAction label="Editar" to={`/dashboard/${type}/${item.id}`}>
                    <Pencil className="size-4.5" />
                  </IconAction>
                  <IconAction label="Eliminar" danger onClick={() => confirm(`¿Eliminar “${titleOf(item as never)}”? No se puede deshacer.`) && remove.mutate(item.id)}>
                    <Trash2 className="size-4.5" />
                  </IconAction>
                </div>
              </li>
            ))}
          </ul>
          <Pagination page={list.data.current_page} lastPage={list.data.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState
          title={`No tienes ${meta.plural.toLowerCase()}`}
          icon={<Tocapu motif={meta.motif} dye={meta.dye} size={40} framed={false} />}
          action={<ButtonLink to={`/dashboard/${type}/nuevo`}>Crear {meta.singular.toLowerCase()}</ButtonLink>}>
          {type === 'promociones' ? 'Las promociones se asocian a un local: regístralo primero si aún no lo tienes.' : 'Lo que publiques aparecerá en el sitio público.'}
        </EmptyState>
      )}
    </>
  )
}
