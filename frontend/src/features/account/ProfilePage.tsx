import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState, type FormEvent } from 'react'
import { useAuth } from '@/shared/auth/AuthContext'
import { PageHeader } from '@/shared/ui/PageHeader'
import { PanelCard } from '@/shared/ui/Panel'
import { Button } from '@/shared/ui/Button'
import { Alert, Spinner } from '@/shared/ui/Feedback'
import { TextArea, TextField } from '@/shared/ui/Field'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { User } from '@/shared/api/types'
import { accountKeys, accountQueries } from '@/features/account/api'

export function ProfilePage() {
  useDocumentTitle('Perfil')
  const { refresh } = useAuth()
  const notify = useToast()
  const qc = useQueryClient()
  const { data: profile, isLoading } = useQuery(accountQueries.profile())

  if (isLoading || !profile) return <Spinner />
  return (
    <>
      <PageHeader title="Perfil" description={profile.email} />
      <ProfileForm
        profile={profile}
        onSaved={() => {
          void qc.invalidateQueries({ queryKey: accountKeys.profile() })
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
    <form onSubmit={onSubmit} className="flex max-w-2xl flex-col gap-6">
      <PanelCard title="Datos personales">
      <fieldset className="flex flex-col gap-4">
        <legend className="sr-only">Datos personales</legend>
        <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required />
        <TextField label="Teléfono" type="tel" value={form.phone} onChange={set('phone')} error={errors.phone} />
        <TextArea label="Sobre ti" value={form.bio} onChange={set('bio')} error={errors.bio} maxLength={1000} />
      </fieldset>
      </PanelCard>
      <PanelCard title="Cambiar contraseña">
      <fieldset className="flex flex-col gap-4">
        <legend className="sr-only">Cambiar contraseña</legend>
        <p className="text-sm text-ink-soft">Déjalo en blanco si no quieres cambiarla.</p>
        <TextField label="Contraseña actual" type="password" autoComplete="current-password" value={form.current_password} onChange={set('current_password')} error={errors.current_password} />
        <TextField label="Nueva contraseña" type="password" autoComplete="new-password" value={form.password} onChange={set('password')} error={errors.password} hint="Mínimo 8 caracteres con mayúsculas, minúsculas, un número y un símbolo." />
        <TextField label="Repite la nueva contraseña" type="password" autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} />
      </fieldset>
      </PanelCard>
      {errors._ && <Alert tone="error">{errors._}</Alert>}
      <div>
        <Button type="submit" loading={save.isPending} loadingText="Guardando…">Guardar cambios</Button>
      </div>
    </form>
  )
}
