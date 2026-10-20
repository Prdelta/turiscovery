import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ChevronLeft } from 'lucide-react'
import { useState } from 'react'
import { useNavigate, useParams } from 'react-router'
import { ContentCard } from '../components/ContentCard'
import { HostCard, HostPortrait, LANGUAGE_LABEL } from '../components/HostVoice'
import { Tocapu, WovenBand } from '../components/textile/Weave'
import { EmptyState, ErrorState, Spinner } from '../components/ui/Feedback'
import { getData, parseApiError } from '../lib/api'
import { useDocumentTitle } from '../lib/hooks'
import type { Anfitrion, HostLanguage } from '../lib/types'

export default function HostsPage() {
  useDocumentTitle('Anfitriones')
  const [language, setLanguage] = useState<HostLanguage | ''>('')
  const { data, isLoading, isError, error, refetch } = useQuery({
    queryKey: ['anfitriones', language],
    queryFn: () => getData<Anfitrion[]>('/anfitriones', { language: language || undefined }),
  })

  return (
    <div className="container-page py-12 sm:py-16">
      <header className="mb-10">
        <div className="flex items-center gap-4">
          <Tocapu motif="persona" dye="qolle" size={48} />
          <h1 className="t-h1">Quién te recibe</h1>
        </div>
        <p className="prose-measure mt-4 text-lg text-ink-soft">
          Familias de las islas flotantes, tejedores de Taquile, guías de Sillustani. Escucha cómo te saludan en su idioma antes de conocerlos.
        </p>
        <WovenBand motif="persona" dye="qolle" height={12} className="mt-6 rounded-sm" />
      </header>

      <div role="group" aria-label="Idioma" className="mb-8 flex flex-wrap gap-2">
        {(['', 'quechua', 'aimara', 'espanol', 'ingles'] as const).map((l) => (
          <button
            key={l || 'todos'}
            onClick={() => setLanguage(l)}
            aria-pressed={language === l}
            className={clsx(
              'inline-flex min-h-11 items-center gap-1.5 whitespace-nowrap rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors',
              language === l ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky',
            )}
          >
            {l ? `Habla ${LANGUAGE_LABEL[l].toLowerCase()}` : 'Todos'}
          </button>
        ))}
      </div>

      {isLoading ? (
        <Spinner />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : data?.length ? (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
          {data.map((h) => <HostPortrait key={h.id} host={h} />)}
        </div>
      ) : (
        <EmptyState title="Aún no hay anfitriones con ese idioma">Los socios presentan aquí a las personas que reciben a los viajeros.</EmptyState>
      )}
    </div>
  )
}

export function HostDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { data: host, isLoading, error, refetch } = useQuery({
    queryKey: ['anfitrion', id],
    queryFn: () => getData<Anfitrion>(`/anfitriones/${id}`),
  })
  useDocumentTitle(host?.name ?? 'Anfitrión')

  if (isLoading) return <Spinner />
  if (!host) return <div className="container-page py-20"><ErrorState message={parseApiError(error).message} onRetry={() => refetch()} /></div>

  return (
    <article className="container-page py-8 sm:py-12">
      <button onClick={() => navigate(-1)} className="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink-soft hover:text-ink">
        <ChevronLeft className="size-4" /> Volver
      </button>
      <div className="grid gap-10 lg:grid-cols-[1fr_1.2fr]">
        <div>
          {host.photo_url ? (
            <img src={host.photo_url} alt={host.name} className="aspect-[4/5] w-full rounded-t-[var(--radius-card)] object-cover" />
          ) : (
            <div className="flex aspect-[4/5] items-center justify-center rounded-t-[var(--radius-card)] bg-lake">
              <Tocapu motif="persona" dye="qolle" size={140} />
            </div>
          )}
          <WovenBand motif="persona" dye="qolle" height={18} />
        </div>
        <div className="flex flex-col gap-6">
          <HostCard host={host} />
          {host.story && (
            <section>
              <h2 className="mb-3 t-h2">Su historia</h2>
              <p className="prose-measure whitespace-pre-line text-lg leading-relaxed">{host.story}</p>
            </section>
          )}
        </div>
      </div>
      {host.experiencias?.length ? (
        <section className="mt-16">
          <h2 className="mb-6 t-h2">Viaja con {host.name.split(' ')[0]}</h2>
          <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            {host.experiencias.map((e) => <ContentCard key={e.id} item={e} type="experiencias" />)}
          </div>
        </section>
      ) : null}
    </article>
  )
}
