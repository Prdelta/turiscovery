import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Pencil, Plus, Trash2 } from 'lucide-react'
import { useState, type FormEvent } from 'react'
import { LANGUAGE_LABEL, VoiceRecorder } from '../../components/HostVoice'
import { ImageUploader } from '../../components/ImageUploader'
import { PageHeader } from '../../components/layout/PanelLayout'
import { Avatar } from '../../components/layout/SiteLayout'
import { Button } from '../../components/ui/Button'
import { Alert, Badge, EmptyState, Spinner } from '../../components/ui/Feedback'
import { Checkbox, TextArea, TextField } from '../../components/ui/Field'
import { useToast } from '../../components/ui/Toast'
import { api, getData, parseApiError } from '../../lib/api'
import { useDocumentTitle } from '../../lib/hooks'
import type { Anfitrion, HostLanguage } from '../../lib/types'

export default function HostsManager() {
  useDocumentTitle('Anfitriones')
  const qc = useQueryClient()
  const notify = useToast()
  const { data, isLoading } = useQuery({ queryKey: ['my-anfitriones'], queryFn: () => getData<Anfitrion[]>('/my-anfitriones') })
  const [editing, setEditing] = useState<Anfitrion | 'new' | null>(null)
  const remove = useMutation({
    mutationFn: (id: number) => api.delete(`/anfitriones/${id}`),
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['my-anfitriones'] })
      notify('Anfitrión eliminado')
    },
    onError: (e) => notify(parseApiError(e).message, 'error'),
  })

  if (editing) return <HostForm host={editing === 'new' ? undefined : editing} onDone={() => setEditing(null)} />

  return (
    <>
      <PageHeader
        title="Anfitriones"
        description="Presenta a las personas que reciben a los viajeros en tus experiencias. Con su foto, su historia y un saludo en su idioma, el viajero sabe con quién va a compartir."
        action={<Button onClick={() => setEditing('new')} icon={<Plus className="size-4.5" />}>Nuevo anfitrión</Button>}
      />
      {isLoading ? (
        <Spinner />
      ) : data?.length ? (
        <ul className="flex flex-col gap-3">
          {data.map((h) => (
            <li key={h.id} className={clsx('flex flex-wrap items-center gap-4 card p-4', !h.is_active && 'border-dashed')}>
              {h.photo_url ? <img src={h.photo_url} alt="" className="size-14 rounded-[var(--radius-control)] border-2 border-ink object-cover" /> : <Avatar name={h.name} />}
              <div className="min-w-0 flex-1">
                <p className="font-bold">{h.name}</p>
                <p className="text-sm text-ink-soft">
                  {[h.role_title, h.community].filter(Boolean).join(' · ') || 'Sin descripción'} · {h.experiencias_count ?? 0} experiencias
                </p>
              </div>
              {h.audio_url ? <Badge className="border-2 border-ink bg-qolle text-ink">Con saludo</Badge> : <Badge>Sin saludo</Badge>}
              <button onClick={() => setEditing(h)} aria-label="Editar" className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-ink bg-white text-ink hover:bg-sky"><Pencil className="size-4.5" /></button>
              <button
                onClick={() => confirm(`¿Eliminar a ${h.name}? Sus experiencias quedarán sin anfitrión.`) && remove.mutate(h.id)}
                aria-label="Eliminar"
                className="inline-flex size-11 shrink-0 items-center justify-center rounded-[var(--radius-control)] border-2 border-cochinilla text-cochinilla hover:bg-cochinilla hover:text-white"
              >
                <Trash2 className="size-4.5" />
              </button>
            </li>
          ))}
        </ul>
      ) : (
        <EmptyState title="Aún no presentas a nadie" action={<Button onClick={() => setEditing('new')}>Presentar a un anfitrión</Button>}>
          Las experiencias con anfitrión muestran su retrato y su voz en la ficha.
        </EmptyState>
      )}
    </>
  )
}

function HostForm({ host, onDone }: { host?: Anfitrion; onDone: () => void }) {
  const qc = useQueryClient()
  const notify = useToast()
  const [form, setForm] = useState({
    name: host?.name ?? '',
    role_title: host?.role_title ?? '',
    community: host?.community ?? '',
    story: host?.story ?? '',
    greeting: host?.greeting ?? '',
    greeting_translation: host?.greeting_translation ?? '',
    is_active: host?.is_active ?? true,
  })
  const [languages, setLanguages] = useState<HostLanguage[]>(host?.languages ?? [])
  const [photo, setPhoto] = useState<string[]>(host?.photo_url ? [host.photo_url] : [])
  const [audio, setAudio] = useState<string | null>(host?.audio_url ?? null)
  const [errors, setErrors] = useState<Record<string, string>>({})

  const save = useMutation({
    mutationFn: () => {
      const body = { ...form, languages, photo_url: photo[0] ?? null, audio_url: audio }
      return host ? api.put(`/anfitriones/${host.id}`, body) : api.post('/anfitriones', body)
    },
    onSuccess: () => {
      void qc.invalidateQueries({ queryKey: ['my-anfitriones'] })
      void qc.invalidateQueries({ queryKey: ['anfitriones'] })
      notify(host ? 'Anfitrión actualizado' : 'Anfitrión creado')
      onDone()
    },
    onError: (e) => {
      const p = parseApiError(e)
      setErrors(Object.keys(p.fields).length ? p.fields : { _: p.message })
    },
  })
  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => setForm((f) => ({ ...f, [k]: e.target.value }))
  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    setErrors({})
    save.mutate()
  }

  return (
    <>
      <PageHeader title={host ? `Editar: ${host.name}` : 'Nuevo anfitrión'} />
      <form onSubmit={onSubmit} className="flex max-w-3xl flex-col gap-6">
        <div className="grid gap-5 card p-6 sm:grid-cols-2">
          <TextField label="Nombre" value={form.name} onChange={set('name')} error={errors.name} required placeholder="Juana Coila" />
          <TextField label="Qué hace" value={form.role_title} onChange={set('role_title')} placeholder="Tejedora, guía, pescador…" />
          <TextField label="Comunidad o isla" value={form.community} onChange={set('community')} placeholder="Uros Titino" wrapClassName="sm:col-span-2" />
          <fieldset className="sm:col-span-2">
            <legend className="mb-2 text-sm font-semibold">Idiomas que habla</legend>
            <div className="flex flex-wrap gap-4">
              {(Object.keys(LANGUAGE_LABEL) as HostLanguage[]).map((l) => (
                <Checkbox
                  key={l}
                  label={LANGUAGE_LABEL[l]}
                  checked={languages.includes(l)}
                  onChange={(e) => setLanguages((ls) => (e.target.checked ? [...ls, l] : ls.filter((x) => x !== l)))}
                />
              ))}
            </div>
          </fieldset>
          <TextArea label="Su historia" value={form.story} onChange={set('story')} error={errors.story} maxLength={3000} wrapClassName="sm:col-span-2" hint="Cuéntala con sus palabras: de dónde es, qué hace, qué le gusta compartir." />
        </div>

        <div className="grid gap-5 card p-6 sm:grid-cols-2">
          <TextField label="Saludo en su idioma" value={form.greeting} onChange={set('greeting')} error={errors.greeting} placeholder="Kamisaraki" />
          <TextField label="Traducción" value={form.greeting_translation} onChange={set('greeting_translation')} placeholder="¿Cómo estás?" />
          <div className="sm:col-span-2">
            <VoiceRecorder value={audio} onChange={setAudio} />
          </div>
        </div>

        <div className="card p-6">
          <ImageUploader value={photo} onChange={setPhoto} folder="anfitriones" multiple={false} label="Retrato" />
        </div>

        <Checkbox label="Visible en el sitio" checked={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.checked }))} />
        {errors._ && <Alert tone="error">{errors._}</Alert>}
        <div className="flex gap-3">
          <Button type="submit" loading={save.isPending}>{host ? 'Guardar cambios' : 'Crear anfitrión'}</Button>
          <Button type="button" variant="secondary" onClick={onDone}>Cancelar</Button>
        </div>
      </form>
    </>
  )
}
