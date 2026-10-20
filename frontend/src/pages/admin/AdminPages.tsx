import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ExternalLink, Pencil, Plus, Power, Search, Trash2 } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { useAuth } from '../../auth/AuthContext'
import { ImageUploader } from '../../components/ImageUploader'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Avatar } from '../../components/layout/SiteLayout'
import { Button, ButtonLink } from '../../components/ui/Button'
import type { Dye, Motif } from '../../components/textile/Weave'
import { Alert, Badge, EmptyState, ErrorState, Pagination, Spinner, StatusBadge } from '../../components/ui/Feedback'
import { ChipGroup, PanelCard, StatTile } from '../../components/ui/Panel'
import { Checkbox, SelectField, TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError, type Paginated } from '../../lib/api'
import { formatDate } from '../../lib/format'
import { useDebounced, useDocumentTitle } from '../../lib/hooks'
import type { Danza, GalleryPhoto, Role, User } from '../../lib/types'

const ROLES: Record<Role, string> = { tourist: 'Viajero', socio: 'Socio', admin: 'Administrador' }

/** Pestaña de rol: tinta para administradores, q'olle (solo relleno) para socios */
function RoleBadge({ role }: { role: Role }) {
  const cls = role === 'admin' ? 'border-2 border-ink bg-ink text-white' : role === 'socio' ? 'border-2 border-ink bg-qolle text-ink' : undefined
  return <Badge className={cls}>{ROLES[role]}</Badge>
}

const TILE_WEAVE: Record<string, [Motif, Dye]> = {
  Usuarios: ['persona', 'lago'],
  Viajeros: ['isla', 'lago'],
  'Socios activos': ['llave', 'qolle'],
  'Socios nuevos este mes': ['sol', 'solOscuro'],
  Locales: ['llave', 'tierra'],
  Experiencias: ['olas', 'anil'],
  Eventos: ['chakana', 'cochinilla'],
  Promociones: ['ojo', 'chilca'],
}

/* ---------- Supervisión ---------- */

interface Supervision {
  stats: Record<string, number>
  socios_mas_activos: User[]
  socios_inactivos: User[]
}

export function AdminHome() {
  useDocumentTitle('Supervisión')
  const { data, isLoading, isError, error, refetch } = useQuery({ queryKey: ['supervision'], queryFn: () => getData<Supervision>('/admin/supervision') })
  if (isLoading) return <Spinner />
  if (isError || !data) return <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
  const s = data.stats
  const tiles: [string, number, string?][] = [
    ['Usuarios', s.total_usuarios],
    ['Viajeros', s.turistas],
    ['Socios activos', s.socios_activos, `${s.socios_inactivos} desactivados`],
    ['Socios nuevos este mes', s.socios_nuevos_mes],
    ['Locales', s.total_locales],
    ['Experiencias', s.total_experiencias],
    ['Eventos', s.total_eventos],
    ['Promociones', s.total_promociones],
  ]
  return (
    <>
      <PageHeader title="Supervisión" description="Estado general de usuarios, socios y contenido." action={<ButtonLink to="/admin/usuarios/nuevo" icon={<Plus className="size-4.5" />}>Crear socio</ButtonLink>} />
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {tiles.map(([label, value, note]) => (
          <StatTile key={label} value={value} label={label} note={note} motif={TILE_WEAVE[label]?.[0] ?? 'chakana'} dye={TILE_WEAVE[label]?.[1] ?? 'morado'} />
        ))}
      </div>
      <div className="mt-10 grid gap-6 xl:grid-cols-2">
        <PanelCard title="Socios con más contenido">
          {data.socios_mas_activos.length ? (
            <table className="w-full text-left text-sm">
              <thead className="t-label text-ink-soft">
                <tr><th className="pb-2">Socio</th><th className="pb-2 text-right">Loc.</th><th className="pb-2 text-right">Exp.</th><th className="pb-2 text-right">Evt.</th><th className="pb-2 text-right">Prom.</th></tr>
              </thead>
              <tbody className="divide-y-2 divide-line">
                {data.socios_mas_activos.map((u) => (
                  <tr key={u.id}>
                    <td className="py-2.5"><Link to={`/admin/usuarios/${u.id}`} className="font-bold hover:text-primary-dark hover:underline">{u.name}</Link></td>
                    <td className="text-right">{u.locales_count}</td>
                    <td className="text-right">{u.experiencias_count}</td>
                    <td className="text-right">{u.eventos_count}</td>
                    <td className="text-right">{u.promociones_count}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <p className="text-ink-soft">Aún no hay socios.</p>
          )}
        </PanelCard>
        <PanelCard title="Socios desactivados">
          {data.socios_inactivos.length ? (
            <ul className="divide-y-2 divide-line">
              {data.socios_inactivos.map((u) => (
                <li key={u.id} className="py-2.5 text-sm">
                  <Link to={`/admin/usuarios/${u.id}`} className="font-bold hover:text-primary-dark hover:underline">{u.name}</Link>
                  <span className="text-ink-soft"> · desde el {formatDate(u.deactivated_at)}{u.deactivated_by ? ` por ${u.deactivated_by.name}` : ''}</span>
                </li>
              ))}
            </ul>
          ) : (
            <p className="text-ink-soft">Todos los socios están activos.</p>
          )}
        </PanelCard>
      </div>
    </>
  )
}

/* ---------- Usuarios ---------- */

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

  const { data, isLoading, isError, error, refetch } = useQuery({
    queryKey: ['admin-users', debounced, role, status, page],
    queryFn: () => getData<Paginated<User>>('/admin/users', { search: debounced, role, status, page }),
  })
  const invalidate = () => {
    void qc.invalidateQueries({ queryKey: ['admin-users'] })
    void qc.invalidateQueries({ queryKey: ['supervision'] })
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

export function UserFormPage() {
  const { id } = useParams()
  const isEdit = !!id && id !== 'nuevo'
  useDocumentTitle(isEdit ? 'Editar usuario' : 'Crear usuario')
  const { data, isLoading } = useQuery({ queryKey: ['admin-user', id], queryFn: () => getData<User>(`/admin/users/${id}`), enabled: isEdit })
  if (isEdit && isLoading) return <Spinner />
  return <UserForm key={id} user={isEdit ? data : undefined} />
}

function UserForm({ user }: { user?: User }) {
  const navigate = useNavigate()
  const qc = useQueryClient()
  const notify = useToast()
  const [form, setForm] = useState({
    name: user?.name ?? '',
    email: user?.email ?? '',
    role: user?.role ?? 'socio',
    phone: user?.phone ?? '',
    bio: user?.bio ?? '',
    password: '',
    password_confirmation: '',
  })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const save = useMutation({
    mutationFn: () => {
      const body: Record<string, string> = { ...form }
      if (user && !form.password) { delete body.password; delete body.password_confirmation }
      return user ? api.put(`/admin/users/${user.id}`, body) : api.post('/admin/users', body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['admin-users'] })
      void qc.invalidateQueries({ queryKey: ['supervision'] })
      notify(user ? 'Usuario actualizado' : 'Usuario creado')
      navigate('/admin/usuarios')
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message })
    },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))
  const onSubmit = (e: FormEvent) => { e.preventDefault(); setErrors({}); save.mutate() }

  return (
    <>
      <PageHeader
        title={user ? `Editar: ${user.name}` : 'Crear usuario'}
        description={user ? `Contenido: ${user.locales_count ?? 0} locales, ${user.experiencias_count ?? 0} experiencias, ${user.eventos_count ?? 0} eventos, ${user.promociones_count ?? 0} promociones.` : 'Los socios se crean aquí; los viajeros se registran solos.'}
      />
      <form onSubmit={onSubmit} className="flex max-w-2xl flex-col gap-5 card p-6">
        <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required />
        <TextField label="Correo" type="email" value={form.email} onChange={set('email')} error={errors.email} required />
        <SelectField label="Rol" options={ROLES} value={form.role} onChange={set('role')} error={errors.role} required />
        <TextField label="Teléfono" type="tel" value={form.phone} onChange={set('phone')} error={errors.phone} />
        <TextArea label="Descripción" value={form.bio} onChange={set('bio')} error={errors.bio} />
        <TextField
          label={user ? 'Nueva contraseña' : 'Contraseña'}
          type="password"
          autoComplete="new-password"
          value={form.password}
          onChange={set('password')}
          error={errors.password}
          required={!user}
          hint={user ? 'Déjala vacía para no cambiarla.' : 'Mínimo 8 caracteres con mayúsculas, minúsculas, un número y un símbolo.'}
        />
        <TextField label="Repite la contraseña" type="password" autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} required={!user} />
        {errors._ && <Alert tone="error">{errors._}</Alert>}
        <div className="flex gap-3">
          <Button type="submit" loading={save.isPending}>{user ? 'Guardar cambios' : 'Crear usuario'}</Button>
          <ButtonLink to="/admin/usuarios" variant="secondary">Cancelar</ButtonLink>
        </div>
      </form>
    </>
  )
}

/* ---------- Danzas ---------- */

const EMPTY_DANZA = { name: '', type: 'mestiza', description: '', history: '', characteristics: '', region: '', image_url: '', video_url: '', order: '0', is_featured: false, is_active: true }

export function DanzasAdmin() {
  useDocumentTitle('Danzas')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery({ queryKey: ['admin-danzas'], queryFn: () => getData<Danza[]>('/admin/candelaria-danzas') })
  const [editing, setEditing] = useState<Danza | 'new' | null>(null)
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/candelaria-danzas/${id}`),
    onSuccess: () => { void qc.invalidateQueries({ queryKey: ['admin-danzas'] }); void qc.invalidateQueries({ queryKey: ['danzas'] }); notify('Danza eliminada') },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (editing) return <DanzaForm danza={editing === 'new' ? undefined : editing} onDone={() => setEditing(null)} />

  return (
    <>
      <PageHeader title="Danzas de la Candelaria" action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Nueva danza</Button>} />
      {isLoading ? <Spinner /> : data?.length ? (
        <ul className="flex flex-col gap-2">
          {data.map((d) => (
            <li key={d.id} className={clsx('flex flex-wrap items-center gap-4 card p-4', !d.is_active && 'border-dashed')}>
              <img src={d.image_url || '/images/puno_dancers.webp'} alt="" className="size-14 rounded-[var(--radius-control)] border-2 border-ink object-cover" />
              <div className="min-w-0 flex-1">
                <p className="font-bold">{d.name}</p>
                <p className="text-sm text-ink-soft">{d.type === 'autoctona' ? 'Autóctona' : 'Mestiza'}{d.region ? ` · ${d.region}` : ''} · orden {d.order ?? 0}</p>
              </div>
              {d.is_featured && <Badge className="border-2 border-sun-dark bg-sun text-ink">Destacada</Badge>}
              {!d.is_active && <StatusBadge status="hidden" label="Oculta" />}
              <button onClick={() => setEditing(d)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></button>
              <button onClick={() => confirm(`¿Eliminar “${d.name}”?`) && remove.mutate(d.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"><Trash2 className="size-4.5" /></button>
            </li>
          ))}
        </ul>
      ) : <EmptyState title="No hay danzas registradas" />}
    </>
  )
}

function DanzaForm({ danza, onDone }: { danza?: Danza; onDone: () => void }) {
  const qc = useQueryClient()
  const notify = useToast()
  const [form, setForm] = useState(() => (danza ? {
    ...EMPTY_DANZA,
    ...Object.fromEntries(Object.entries(danza).map(([k, v]) => [k, v ?? ''])),
    order: String(danza.order ?? 0),
  } : EMPTY_DANZA) as typeof EMPTY_DANZA)
  const [errors, setErrors] = useState<Record<string, string>>({})
  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, order: Number(form.order) || 0, image_url: form.image_url || null, video_url: form.video_url || null }
      return danza ? api.put(`/candelaria-danzas/${danza.id}`, body) : api.post('/candelaria-danzas', body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['admin-danzas'] })
      void qc.invalidateQueries({ queryKey: ['danzas'] })
      notify(danza ? 'Danza actualizada' : 'Danza creada')
      onDone()
    },
    onError: (e) => { const p = parseApiError(e); setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message }) },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))

  return (
    <>
      <PageHeader title={danza ? `Editar: ${danza.name}` : 'Nueva danza'} />
      <form onSubmit={(e) => { e.preventDefault(); setErrors({}); save.mutate() }} className="grid max-w-3xl gap-5 card p-6 sm:grid-cols-2">
        <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required />
        <SelectField label="Tipo" options={{ autoctona: 'Autóctona', mestiza: 'Mestiza' }} value={form.type} onChange={set('type')} required />
        <TextField label="Región o comunidad" value={form.region} onChange={set('region')} error={errors.region} />
        <TextField label="Orden" type="number" min={0} value={form.order} onChange={set('order')} />
        <TextArea label="Descripción" value={form.description} onChange={set('description')} error={errors.description} required wrapClassName="sm:col-span-2" />
        <TextArea label="Historia" value={form.history} onChange={set('history')} wrapClassName="sm:col-span-2" />
        <TextArea label="Características" value={form.characteristics} onChange={set('characteristics')} wrapClassName="sm:col-span-2" />
        <TextField label="Video (YouTube o Vimeo)" type="url" value={form.video_url} onChange={set('video_url')} error={errors.video_url} wrapClassName="sm:col-span-2" />
        <div className="sm:col-span-2">
          <ImageUploader value={form.image_url ? [form.image_url] : []} onChange={(urls) => setForm((f) => ({ ...f, image_url: urls[0] ?? '' }))} folder="candelaria/danzas" multiple={false} label="Foto" />
        </div>
        <Checkbox label="Destacada" checked={form.is_featured} onChange={(e) => setForm((f) => ({ ...f, is_featured: e.target.checked }))} />
        <Checkbox label="Visible en el sitio" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} />
        {errors._ && <div className="sm:col-span-2"><Alert tone="error">{errors._}</Alert></div>}
        <div className="flex gap-3 sm:col-span-2">
          <Button type="submit" loading={save.isPending}>{danza ? 'Guardar cambios' : 'Crear danza'}</Button>
          <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
        </div>
      </form>
    </>
  )
}

/* ---------- Galería ---------- */

export function GalleryAdmin() {
  useDocumentTitle('Galería Candelaria')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery({ queryKey: ['admin-gallery'], queryFn: () => getData<GalleryPhoto[]>('/admin/candelaria-gallery') })
  const [editing, setEditing] = useState<GalleryPhoto | 'new' | null>(null)
  const invalidate = () => { void qc.invalidateQueries({ queryKey: ['admin-gallery'] }); void qc.invalidateQueries({ queryKey: ['gallery'] }) }
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/candelaria-gallery/${id}`),
    onSuccess: () => { invalidate(); notify('Foto eliminada') },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <>
      <PageHeader title="Galería de la Candelaria" action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Añadir foto</Button>} />
      {editing && <PhotoForm photo={editing === 'new' ? undefined : editing} onDone={() => { setEditing(null); invalidate() }} />}
      {isLoading ? <Spinner /> : data?.length ? (
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
          {data.map((p) => (
            <figure key={p.id} className={clsx('overflow-hidden card', !p.is_active && 'border-dashed')}>
              <img src={p.image_url} alt={p.title} className="aspect-[4/3] w-full object-cover" />
              <figcaption className="flex items-start gap-2 p-3">
                <div className="min-w-0 flex-1">
                  <p className="truncate font-semibold">{p.title}</p>
                  <p className="text-sm text-ink-soft">{p.year}{!p.is_active && ' · oculta'}</p>
                </div>
                <button onClick={() => setEditing(p)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4" /></button>
                <button onClick={() => confirm('¿Eliminar esta foto?') && remove.mutate(p.id)} aria-label="Eliminar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"><Trash2 className="size-4" /></button>
              </figcaption>
            </figure>
          ))}
        </div>
      ) : <EmptyState title="La galería está vacía" />}
    </>
  )
}

function PhotoForm({ photo, onDone }: { photo?: GalleryPhoto; onDone: () => void }) {
  const notify = useToast()
  const [form, setForm] = useState({
    title: photo?.title ?? '',
    description: photo?.description ?? '',
    image_url: photo?.image_url ?? '',
    year: String(photo?.year ?? new Date().getFullYear()),
    order: String(photo?.order ?? 0),
    is_active: photo?.is_active ?? true,
  })
  const [error, setError] = useState<string | null>(null)
  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, year: Number(form.year), order: Number(form.order) || 0 }
      return photo ? api.put(`/candelaria-gallery/${photo.id}`, body) : api.post('/candelaria-gallery', body)
    },
    onSuccess: () => { notify(photo ? 'Foto actualizada' : 'Foto añadida'); onDone() },
    onError: (e) => setError(parseApiError(e).message),
  })
  return (
    <form
      onSubmit={(e) => {
        e.preventDefault()
        if (!form.image_url) { setError('Sube una imagen.'); return }
        save.mutate()
      }}
      className="mb-8 grid gap-5 rounded-[var(--radius-card)] border-2 border-primary/30 bg-white p-6 sm:grid-cols-2"
    >
      <h2 className="t-h3 sm:col-span-2">{photo ? 'Editar foto' : 'Nueva foto'}</h2>
      <div className="sm:col-span-2">
        <ImageUploader value={form.image_url ? [form.image_url] : []} onChange={(u) => setForm((f) => ({ ...f, image_url: u[0] ?? '' }))} folder="candelaria/gallery" multiple={false} label="Imagen" />
      </div>
      <TextField label="Título" value={form.title} onChange={(e) => setForm((f) => ({ ...f, title: e.target.value }))} required />
      <TextField label="Año" type="number" min={1583} max={new Date().getFullYear() + 1} value={form.year} onChange={(e) => setForm((f) => ({ ...f, year: e.target.value }))} required />
      <TextArea label="Descripción" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} wrapClassName="sm:col-span-2" className="min-h-20" />
      <TextField label="Orden" type="number" min={0} value={form.order} onChange={(e) => setForm((f) => ({ ...f, order: e.target.value }))} />
      <div className="flex items-end"><Checkbox label="Visible en el sitio" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} /></div>
      {error && <div className="sm:col-span-2"><Alert tone="error">{error}</Alert></div>}
      <div className="flex gap-3 sm:col-span-2">
        <Button type="submit" loading={save.isPending}>{photo ? 'Guardar' : 'Añadir a la galería'}</Button>
        <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
      </div>
    </form>
  )
}

/* ---------- Búsqueda de recursos ---------- */

interface WikiSummary { title: string; extract: string; description: string; thumbnail: string | null; url: string }
interface CommonsResult { title: string; snippet: string; page_url: string }
interface ImageResult { url: string; title: string; source: string }

export function ResourceSearchPage() {
  useDocumentTitle('Buscar recursos')
  const [query, setQuery] = useState('')
  const [submitted, setSubmitted] = useState('')
  const wiki = useQuery({
    queryKey: ['wiki', submitted],
    enabled: submitted.length >= 3,
    retry: false,
    queryFn: () => getData<WikiSummary>('/admin/resources/search-wikipedia', { query: submitted }),
  })
  const images = useQuery({
    queryKey: ['images', submitted],
    enabled: submitted.length >= 3,
    queryFn: () => getData<{ unsplash: ImageResult[]; wikimedia: CommonsResult[] }>('/admin/resources/search-images', { query: submitted }),
  })

  return (
    <>
      <PageHeader title="Buscar recursos" description="Consulta Wikipedia y Wikimedia Commons para documentar danzas y fotos. Revisa siempre la licencia antes de usar una imagen." />
      <form onSubmit={(e) => { e.preventDefault(); setSubmitted(query.trim()) }} className="mb-8 flex max-w-xl gap-2">
        <label htmlFor="res-q" className="sr-only">Tema</label>
        <input id="res-q" value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Ejemplo: Diablada puneña" className="field-input" minLength={3} />
        <Button type="submit">Buscar</Button>
      </form>
      {submitted && (
        <div className="grid gap-6 xl:grid-cols-2">
          <PanelCard title="Wikipedia">
            {wiki.isLoading ? <div className="skeleton h-32" /> : wiki.data ? (
              <div className="flex gap-4">
                {wiki.data.thumbnail && <img src={wiki.data.thumbnail} alt="" className="size-24 rounded-[var(--radius-control)] border-2 border-ink object-cover" />}
                <div>
                  <p className="font-bold">{wiki.data.title}</p>
                  {wiki.data.description && <p className="text-sm text-ink-soft">{wiki.data.description}</p>}
                  <p className="mt-2 text-sm">{wiki.data.extract}</p>
                  {wiki.data.url && <a href={wiki.data.url} target="_blank" rel="noreferrer" className="mt-2 inline-flex items-center gap-1 text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">Abrir artículo <ExternalLink className="size-3.5" /></a>}
                </div>
              </div>
            ) : <p className="text-ink-soft">No hay un artículo con ese título exacto. Prueba con el nombre completo.</p>}
          </PanelCard>
          <PanelCard title="Wikimedia Commons">
            {images.isLoading ? <div className="skeleton h-32" /> : images.data?.wikimedia.length ? (
              <ul className="flex flex-col divide-y-2 divide-line">
                {images.data.wikimedia.map((r) => (
                  <li key={r.page_url} className="py-2.5">
                    <a href={r.page_url} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 font-bold text-primary-dark underline decoration-2 underline-offset-4">{r.title.replace(/^File:|^Archivo:/, '')} <ExternalLink className="size-3.5" /></a>
                    {r.snippet && <p className="line-clamp-2 text-sm text-ink-soft">{r.snippet}</p>}
                  </li>
                ))}
              </ul>
            ) : <p className="text-ink-soft">Sin archivos para esa búsqueda.</p>}
          </PanelCard>
        </div>
      )}
    </>
  )
}
