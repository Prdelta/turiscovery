import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState, type FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { Alert, Spinner } from '@/shared/ui/Feedback'
import { SelectField, TextArea, TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { User } from '@/shared/api/types'
import { adminKeys, adminQueries } from '@/features/admin/api'
import { ROLES } from '@/features/admin/roles'

export function UserFormPage() {
  const { id } = useParams()
  const isEdit = !!id && id !== 'nuevo'
  useDocumentTitle(isEdit ? 'Editar usuario' : 'Crear usuario')
  const { data, isLoading } = useQuery({ ...adminQueries.user(id), enabled: isEdit })
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
      void qc.invalidateQueries({ queryKey: adminKeys.all })
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
