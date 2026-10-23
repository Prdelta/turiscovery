import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Pencil, Plus, Power, Search, Trash2 } from 'lucide-react'
import { useState } from 'react'
import { Link } from 'react-router'
import { useAuth } from '@/shared/auth/AuthContext'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Avatar } from '@/shared/ui/Identity'
import { ButtonLink } from '@/shared/ui/Button'
import { EmptyState, ErrorState, Pagination, Spinner, StatusBadge } from '@/shared/ui/Feedback'
import { ChipGroup } from '@/shared/ui/Panel'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { formatDate } from '@/shared/lib/format'
import { useDebounced, useDocumentTitle } from '@/shared/lib/hooks'
import { adminKeys, adminQueries } from '@/features/admin/api'
import { ROLES, RoleBadge } from '@/features/admin/roles'

export function UsersPage() {
  useDocumentTitle('Usuarios y socios')
  const { user: me } = useAuth()
  const qc = useQueryClient()
  const notify = useToast()
  const [search, setSearch] = useState('')
  const [role, setRole] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const debounced = useDebounced(search)

  const { data, isLoading, isError, error, refetch } = useQuery(adminQueries.users({ search: debounced, role, status, page }))
  const invalidate = () => {
    void qc.invalidateQueries({ queryKey: adminKeys.all })
  }
  const toggle = useMutation({
    mutationFn: (id: number) => api.post<{ message: string }>(`/admin/users/${id}/toggle-status`),
    onSuccess: (res) => { invalidate(); notify(res.data.message) },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/admin/users/${id}`),
    onSuccess: () => { invalidate(); notify('Usuario eliminado') },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader title="Usuarios y socios" action={<ButtonLink to="/admin/usuarios/nuevo" icon={<Plus className="size-4.5" />}>Crear usuario</ButtonLink>} />
      <div className="mb-6 flex flex-col gap-3" role="search">
        <div className="relative max-w-md">
          <label htmlFor="u-search" className="sr-only">Buscar por nombre o correo</label>
          <Search className="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-ink-soft" aria-hidden />
          <input id="u-search" type="search" value={search} onChange={(e) => { setSearch(e.target.value); setPage(1) }} placeholder="Nombre o correo" className="field-input pl-12" />
        </div>
        <ChipGroup
          label="Rol"
          value={role}
          onChange={(v) => { setRole(v); setPage(1) }}
          options={[['', 'Todos'], ...(Object.entries(ROLES) as [string, string][])]}
        />
        <ChipGroup
          label="Estado"
          value={status}
          onChange={(v) => { setStatus(v); setPage(1) }}
          options={[['', 'Cualquier estado'], ['active', 'Activos'], ['inactive', 'Desactivados']]}
        />
      </div>
      {isLoading ? (
        <Spinner />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : data?.data.length ? (
        <>
          <ul className="flex flex-col gap-2">
            {data.data.map((u) => (
              <li key={u.id} className={clsx('flex flex-wrap items-center gap-4 card p-4', !u.is_active && 'border-dashed')}>
                <Avatar name={u.name} src={u.avatar} />
                <div className="min-w-0 flex-1">
                  <p className="truncate font-bold">{u.name}{u.id === me?.id && <span className="font-normal text-ink-soft"> (tú)</span>}</p>
                  <p className="truncate text-sm text-ink-soft">{u.email} · desde {formatDate(u.created_at)}</p>
                </div>
                <RoleBadge role={u.role} />
                {!u.is_active && <StatusBadge status="inactive" label="Desactivado" />}
                {u.id !== me?.id && (
                  <div className="flex gap-2">
                    <button onClick={() => toggle.mutate(u.id)} aria-label={u.is_active ? 'Desactivar cuenta' : 'Activar cuenta'} title={u.is_active ? 'Desactivar cuenta' : 'Activar cuenta'} className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky">
                      <Power className="size-4.5" />
                    </button>
                    <Link to={`/admin/usuarios/${u.id}`} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></Link>
                    <button onClick={() => confirm(`¿Eliminar la cuenta de ${u.name}? Se borrará su contenido asociado.`) && remove.mutate(u.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white">
                      <Trash2 className="size-4.5" />
                    </button>
                  </div>
                )}
              </li>
            ))}
          </ul>
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={setPage} />
        </>
      ) : (
        <EmptyState title="No hay usuarios con esos filtros" />
      )}
    </>
  )
}
