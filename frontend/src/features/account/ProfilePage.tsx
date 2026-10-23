import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { useRef, useState, type FormEvent } from 'react'
import { useAuth } from '@/shared/auth/AuthContext'
import { PageHeader } from '@/shared/ui/PageHeader'
import { PanelCard } from '@/shared/ui/Panel'
import { Button } from '@/shared/ui/Button'
import { Alert, FilterChip, Spinner } from '@/shared/ui/Feedback'
import { Checkbox, TextArea, TextField } from '@/shared/ui/Field'
import { Avatar } from '@/shared/ui/Identity'
import { CONTENT } from '@/shared/config/content'
import { useToast } from '@/shared/ui/Toast'
import { api, parseApiError } from '@/shared/api/client'
import { useDocumentTitle } from '@/shared/lib/hooks'
import type { Interest, TravelPace, User, UserPreferences } from '@/shared/api/types'
import { accountKeys, accountQueries } from '@/features/account/api'

export function ProfilePage() {
  useDocumentTitle('Perfil')
  const { refresh } = useAuth()
  const notify = useToast()
  const qc = useQueryClient()
  const { data: profile, isLoading } = useQuery(accountQueries.profile())

  if (isLoading || !profile) return <Spinner />
  const saved = (message: string) => () => {
    void qc.invalidateQueries({ queryKey: accountKeys.profile() })
    void refresh()
    notify(message)
  }
  return (
    <>
      <PageHeader title="Perfil" description={profile.email} />
      <div className="flex max-w-2xl flex-col gap-6">
        <AvatarCard profile={profile} onSaved={saved('Foto actualizada')} />
        <ProfileForm profile={profile} onSaved={saved('Perfil actualizado')} />
        <PreferencesCard key={JSON.stringify(profile.preferences)} profile={profile} onSaved={saved('Preferencias guardadas')} />
      </div>
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

const INTERESTS: Interest[] = ['experiencias', 'eventos', 'promociones', 'locales', 'candelaria']
const PACES: [TravelPace, string, string][] = [
  ['suave', 'Suave', 'Recién llegado o prefiero caminatas cortas'],
  ['moderado', 'Moderado', 'Me aclimaté y puedo caminar un par de horas'],
  ['intenso', 'Intenso', 'Quiero subir cerros y hacer kayak'],
]

/** Foto de perfil: subir una nueva o quitarla */
function AvatarCard({ profile, onSaved }: { profile: User; onSaved: () => void }) {
  const input = useRef<HTMLInputElement>(null)
  const notify = useToast()
  const upload = useMutation({
    mutationFn: (file: File | null) => {
      const body = new FormData()
      if (file) body.append('avatar', file)
      else body.append('remove', '1')
      return api.post('/profile/avatar', body)
    },
    onSuccess: onSaved,
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  return (
    <PanelCard title="Foto de perfil">
      <div className="flex flex-wrap items-center gap-4">
        <Avatar name={profile.name} src={profile.avatar} size="lg" />
        <div className="flex flex-wrap gap-2">
          <input
            ref={input}
            type="file"
            accept="image/jpeg,image/png,image/webp"
            className="sr-only"
            aria-label="Elegir foto de perfil"
            onChange={(e) => {
              const file = e.target.files?.[0]
              if (file) upload.mutate(file)
              e.target.value = ''
            }}
          />
          <Button type="button" size="sm" variant="secondary" loading={upload.isPending && upload.variables !== null} loadingText="Subiendo…" onClick={() => input.current?.click()}>
            {profile.avatar ? 'Cambiar foto' : 'Subir foto'}
          </Button>
          {profile.avatar && (
            <Button type="button" size="sm" variant="ghost" loading={upload.isPending && upload.variables === null} loadingText="Quitando…" onClick={() => upload.mutate(null)}>
              Quitar
            </Button>
          )}
        </div>
        <p className="w-full text-sm text-ink-soft">JPG, PNG o WebP de hasta 3 MB. Los anfitriones la ven en tus reservas.</p>
      </div>
    </PanelCard>
  )
}

/** Preferencias: qué le interesa, a qué ritmo viaja y qué correos quiere recibir */
function PreferencesCard({ profile, onSaved }: { profile: User; onSaved: () => void }) {
  const [prefs, setPrefs] = useState<UserPreferences>(
    profile.preferences ?? { notifications: { bookings: true, promotions: true }, interests: [], pace: null },
  )
  const notify = useToast()
  const save = useMutation({
    mutationFn: () => api.put('/profile', { name: profile.name, phone: profile.phone ?? '', bio: profile.bio ?? '', preferences: prefs }),
    onSuccess: onSaved,
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })
  const toggleInterest = (i: Interest) =>
    setPrefs((p) => ({ ...p, interests: p.interests.includes(i) ? p.interests.filter((x) => x !== i) : [...p.interests, i] }))

  return (
    <PanelCard title="Preferencias">
      <div className="flex flex-col gap-6">
        <fieldset>
          <legend className="font-bold">¿Qué te interesa?</legend>
          <p className="text-sm text-ink-soft">Lo mostramos primero en tu inicio.</p>
          <div className="mt-3 flex flex-wrap gap-2">
            {INTERESTS.map((i) => (
              <FilterChip key={i} type="button" selected={prefs.interests.includes(i)} aria-pressed={prefs.interests.includes(i)} onClick={() => toggleInterest(i)}>
                {CONTENT[i].plural}
              </FilterChip>
            ))}
          </div>
        </fieldset>
        <fieldset>
          <legend className="font-bold">Tu ritmo de viaje</legend>
          <p className="text-sm text-ink-soft">Para sugerirte experiencias según la altura y el esfuerzo.</p>
          <div className="mt-3 grid gap-2 sm:grid-cols-3">
            {PACES.map(([value, label, hint]) => (
              <label
                key={value}
                className={clsx(
                  'flex cursor-pointer flex-col gap-1 rounded-[var(--radius-control)] border-2 p-3',
                  prefs.pace === value ? 'border-primary bg-primary/10' : 'border-line hover:border-ink',
                )}
              >
                <span className="flex items-center gap-2 font-bold">
                  <input type="radio" name="pace" className="accent-primary" checked={prefs.pace === value} onChange={() => setPrefs((p) => ({ ...p, pace: value }))} />
                  {label}
                </span>
                <span className="text-sm text-ink-soft">{hint}</span>
              </label>
            ))}
          </div>
        </fieldset>
        <fieldset className="flex flex-col">
          <legend className="font-bold">Correos</legend>
          <Checkbox
            label="Avisos de mis reservas (confirmaciones, cambios y recordatorios)"
            checked={prefs.notifications.bookings}
            onChange={(e) => setPrefs((p) => ({ ...p, notifications: { ...p.notifications, bookings: e.target.checked } }))}
          />
          <Checkbox
            label="Promociones y novedades de Puno"
            checked={prefs.notifications.promotions}
            onChange={(e) => setPrefs((p) => ({ ...p, notifications: { ...p.notifications, promotions: e.target.checked } }))}
          />
        </fieldset>
        <div>
          <Button type="button" loading={save.isPending} loadingText="Guardando…" onClick={() => save.mutate()}>Guardar preferencias</Button>
        </div>
      </div>
    </PanelCard>
  )
}
