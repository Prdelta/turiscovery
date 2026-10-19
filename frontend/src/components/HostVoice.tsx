import { clsx } from 'clsx'
import { Mic, Pause, Play, Square, Trash2, Upload } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router'
import { api, parseApiError } from '../lib/api'
import type { Anfitrion, HostLanguage } from '../lib/types'
import { Avatar } from './layout/SiteLayout'
import { Tocapu, WovenBand } from './textile/Weave'
import { NativePhrase, type NativeLanguage } from './ui/NativePhrase'

export const LANGUAGE_LABEL: Record<HostLanguage, string> = {
  quechua: 'Quechua',
  aimara: 'Aimara',
  espanol: 'Español',
  ingles: 'Inglés',
}

/** Primera lengua originaria que habla el anfitrión (para el atributo lang del saludo) */
function nativeLanguageOf(host: Anfitrion): NativeLanguage | null {
  return (host.languages?.find((l) => l === 'quechua' || l === 'aimara') as NativeLanguage | undefined) ?? null
}

/** Reproductor del saludo grabado: un botón grande y una faja que avanza como un tejido */
export function VoicePlayer({ src, label, dark = false }: { src: string; label: string; dark?: boolean }) {
  const audio = useRef<HTMLAudioElement>(null)
  const [playing, setPlaying] = useState(false)
  const [progress, setProgress] = useState(0)
  const [duration, setDuration] = useState(0)

  return (
    <div className="flex items-center gap-3">
      <button
        type="button"
        onClick={() => (playing ? audio.current?.pause() : audio.current?.play())}
        aria-label={playing ? `Pausar ${label}` : `Escuchar ${label}`}
        className={clsx(
          'inline-flex size-12 shrink-0 items-center justify-center rounded-full transition-transform hover:scale-105',
          dark ? 'bg-mint text-ink' : 'bg-primary text-white',
        )}
      >
        {playing ? <Pause className="size-5" /> : <Play className="ml-0.5 size-5" />}
      </button>
      <div className="min-w-0 flex-1">
        <div className="relative h-3.5 overflow-hidden rounded-sm bg-ink/10" aria-hidden>
          <div className="absolute inset-y-0 left-0 overflow-hidden" style={{ width: `${progress * 100}%` }}>
            <WovenBand motif="persona" dye="qolle" height={14} className="w-[600px]" />
          </div>
        </div>
        <p className={clsx('mt-1 text-xs', dark ? 'text-white/70' : 'text-ink-soft')}>
          {duration ? `${Math.round(duration)} s` : 'Saludo grabado'}
        </p>
      </div>
      <audio
        ref={audio}
        src={src}
        preload="metadata"
        onPlay={() => setPlaying(true)}
        onPause={() => setPlaying(false)}
        onEnded={() => {
          setPlaying(false)
          setProgress(0)
        }}
        onLoadedMetadata={(e) => setDuration(Number.isFinite(e.currentTarget.duration) ? e.currentTarget.duration : 0)}
        onTimeUpdate={(e) => e.currentTarget.duration && setProgress(e.currentTarget.currentTime / e.currentTarget.duration)}
      />
    </div>
  )
}

/** Tarjeta de anfitrión: retrato, saludo en su idioma con traducción y su voz */
export function HostCard({ host, compact = false }: { host: Anfitrion; compact?: boolean }) {
  return (
    <section className="overflow-hidden rounded-[var(--radius-card)] bg-lake-deep text-white" aria-label={`Anfitrión: ${host.name}`}>
      <WovenBand motif="persona" dye="qolle" height={14} />
      <div className={clsx('flex gap-4 p-5', compact ? 'items-center' : 'flex-col sm:flex-row')}>
        {host.photo_url ? (
          <img src={host.photo_url} alt={host.name} className={clsx('shrink-0 rounded-2xl object-cover', compact ? 'size-16' : 'size-28')} />
        ) : (
          <Avatar name={host.name} size="lg" />
        )}
        <div className="min-w-0 flex-1">
          <p className="text-sm font-semibold text-mint">Te recibe</p>
          <Link to={`/anfitriones/${host.id}`} className="font-display text-2xl font-extrabold text-white hover:underline">
            {host.name}
          </Link>
          <p className="text-sm text-white/75">
            {[host.role_title, host.community].filter(Boolean).join(' · ')}
            {host.languages?.length ? ` · habla ${host.languages.map((l) => LANGUAGE_LABEL[l]).join(', ')}` : ''}
          </p>
          {!compact && host.greeting && (
            <blockquote className="mt-4">
              <NativePhrase original={host.greeting} translation={host.greeting_translation} language={nativeLanguageOf(host)} dark />
            </blockquote>
          )}
          {host.audio_url && (
            <div className="mt-4">
              <VoicePlayer src={host.audio_url} label={`el saludo de ${host.name}`} dark />
            </div>
          )}
        </div>
      </div>
    </section>
  )
}

/** Retrato para la galería de anfitriones */
export function HostPortrait({ host }: { host: Anfitrion }) {
  return (
    <Link to={`/anfitriones/${host.id}`} className="group flex flex-col">
      <div className="relative overflow-hidden rounded-t-[var(--radius-card)] bg-lake">
        {host.photo_url ? (
          <img src={host.photo_url} alt="" loading="lazy" className="aspect-[3/4] w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" />
        ) : (
          <div className="flex aspect-[3/4] items-center justify-center">
            <Tocapu motif="persona" dye="qolle" size={96} />
          </div>
        )}
        {host.audio_url && (
          <span className="absolute bottom-3 left-3 inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-ink">
            <Play className="size-3.5" /> Escucha su saludo
          </span>
        )}
      </div>
      <WovenBand motif="persona" dye="qolle" height={12} />
      <h3 className="mt-3 text-xl font-bold group-hover:text-primary">{host.name}</h3>
      <p className="text-sm text-ink-soft">{[host.role_title, host.community].filter(Boolean).join(' · ')}</p>
      {host.languages?.length ? (
        <p className="mt-1 flex flex-wrap gap-1">
          {host.languages.map((l) => (
            <span key={l} className="rounded-[var(--radius-chip)] bg-qolle/25 px-2 py-0.5 text-xs font-bold text-ink">{LANGUAGE_LABEL[l]}</span>
          ))}
        </p>
      ) : null}
    </Link>
  )
}

/** Grabadora del saludo: graba desde el micrófono o sube un archivo de audio */
export function VoiceRecorder({ value, onChange }: { value: string | null; onChange: (url: string | null) => void }) {
  const [recording, setRecording] = useState(false)
  const [seconds, setSeconds] = useState(0)
  const [uploading, setUploading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const recorder = useRef<MediaRecorder | null>(null)
  const chunks = useRef<Blob[]>([])
  const fileInput = useRef<HTMLInputElement>(null)
  const canRecord = typeof window !== 'undefined' && 'MediaRecorder' in window && !!navigator.mediaDevices?.getUserMedia

  useEffect(() => {
    if (!recording) return
    const t = setInterval(() => setSeconds((s) => s + 1), 1000)
    return () => clearInterval(t)
  }, [recording])

  // Un saludo corto: se detiene solo a los 60 segundos
  useEffect(() => {
    if (recording && seconds >= 60) recorder.current?.stop()
  }, [recording, seconds])

  const upload = async (blob: Blob, name: string) => {
    setUploading(true)
    setError(null)
    try {
      const form = new FormData()
      form.append('audio', blob, name)
      const res = await api.post<{ data: { url: string } }>('/uploads/audio', form)
      onChange(res.data.data.url)
    } catch (e) {
      setError(parseApiError(e).message)
    } finally {
      setUploading(false)
    }
  }

  const start = async () => {
    setError(null)
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true })
      const rec = new MediaRecorder(stream)
      chunks.current = []
      rec.ondataavailable = (e) => e.data.size && chunks.current.push(e.data)
      rec.onstop = () => {
        stream.getTracks().forEach((t) => t.stop())
        setRecording(false)
        const type = rec.mimeType || 'audio/webm'
        const ext = type.includes('mp4') ? 'm4a' : type.includes('ogg') ? 'ogg' : 'webm'
        void upload(new Blob(chunks.current, { type }), `saludo.${ext}`)
      }
      recorder.current = rec
      setSeconds(0)
      rec.start()
      setRecording(true)
    } catch {
      setError('No se pudo usar el micrófono. Revisa el permiso del navegador o sube un archivo de audio.')
    }
  }

  return (
    <div className="flex flex-col gap-3">
      <span className="text-sm font-semibold">Saludo grabado</span>
      {value && !recording ? (
        <div className="flex items-center gap-3 rounded-2xl border border-line p-3">
          <div className="flex-1"><VoicePlayer src={value} label="el saludo" /></div>
          <button type="button" onClick={() => onChange(null)} aria-label="Quitar audio" className="rounded-full p-2 hover:bg-danger/10 hover:text-danger">
            <Trash2 className="size-4" />
          </button>
        </div>
      ) : null}
      <div className="flex flex-wrap items-center gap-3">
        {canRecord && (
          <button
            type="button"
            onClick={() => (recording ? recorder.current?.stop() : start())}
            disabled={uploading}
            className={clsx(
              'inline-flex h-11 items-center gap-2 rounded-full px-5 font-semibold text-white',
              recording ? 'bg-danger' : 'bg-cochinilla hover:bg-cochinilla/85',
            )}
          >
            {recording ? <Square className="size-4" /> : <Mic className="size-4" />}
            {recording ? `Detener (${seconds} s)` : value ? 'Grabar de nuevo' : 'Grabar saludo'}
          </button>
        )}
        <button
          type="button"
          onClick={() => fileInput.current?.click()}
          disabled={uploading || recording}
          className="inline-flex h-11 items-center gap-2 rounded-full border border-line bg-white px-5 font-semibold hover:border-ink/40"
        >
          <Upload className="size-4" /> {uploading ? 'Subiendo…' : 'Subir audio'}
        </button>
        <input
          ref={fileInput}
          type="file"
          accept="audio/*"
          hidden
          onChange={(e) => {
            const f = e.target.files?.[0]
            if (f) void upload(f, f.name)
            e.target.value = ''
          }}
        />
      </div>
      <p className="text-sm text-ink-soft">Un saludo corto en su idioma, de hasta un minuto. Pide permiso a la persona antes de grabarla.</p>
      {error && <p className="text-sm text-danger">{error}</p>}
    </div>
  )
}
