import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { ChevronLeft, MapPin, X } from 'lucide-react'
import { useMemo, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router'
import { ContentCard } from '../components/ContentCard'
import { ButtonLink } from '../components/ui/Button'
import { LiveBanner } from './CandelariaLivePage'
import { CardGridSkeleton, ErrorState, Spinner } from '../components/ui/Feedback'
import { getData, parseApiError } from '../lib/api'
import { useContentList, useDanzas, useDocumentTitle, useGallery } from '../lib/hooks'
import type { Candelaria, Danza, GalleryPhoto } from '../lib/types'

const TYPE_LABEL = { autoctona: 'Autóctona', mestiza: 'Mestiza' } as const

export default function CandelariaPage() {
  useDocumentTitle('Candelaria')
  return (
    <>
      <LiveBanner />
      <section className="bg-lake-deep text-white">
        <div className="container-page grid items-end gap-10 py-16 sm:py-24 lg:grid-cols-[1.2fr_1fr]">
          <div>
            <p className="mb-4 font-semibold text-mint">Patrimonio Cultural Inmaterial de la Humanidad · UNESCO 2014</p>
            <h1 className="font-display text-[clamp(2.5rem,6vw,4.8rem)] font-extrabold leading-[1] text-white">
              Festividad de la Virgen de la Candelaria
            </h1>
          </div>
          <p className="prose-measure text-lg text-white/85">
            Durante la primera mitad de febrero, conjuntos de danza autóctona —de las comunidades quechuas y aimaras— y de trajes de luces
            —mestizos, con bandas de metales— recorren Puno en honor a la Virgen. Aquí reunimos sus danzas, su historia y fotos de cada año.
          </p>
          <div className="lg:col-start-2">
            <ButtonLink to="/candelaria/en-vivo" variant="mint">Programa y desfile en vivo</ButtonLink>
          </div>
        </div>
      </section>
      <DanzasSection />
      <GallerySection />
      <PostsSection />
    </>
  )
}

function DanzasSection() {
  const { data, isLoading, isError, error, refetch } = useDanzas()
  const [filter, setFilter] = useState<'all' | Danza['type']>('all')
  const danzas = (data ?? []).filter((d) => filter === 'all' || d.type === filter)

  return (
    <section aria-labelledby="danzas" className="container-page py-20">
      <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h2 id="danzas" className="text-3xl font-extrabold sm:text-4xl">Danzas</h2>
          <p className="mt-2 max-w-xl text-ink-soft">Las autóctonas se bailan con instrumentos de viento andinos; las mestizas, con trajes bordados y bandas.</p>
        </div>
        <div role="group" aria-label="Filtrar danzas" className="flex rounded-full bg-white p-1">
          {(['all', 'autoctona', 'mestiza'] as const).map((f) => (
            <button
              key={f}
              onClick={() => setFilter(f)}
              aria-pressed={filter === f}
              className={clsx('rounded-full px-4 py-2 text-sm font-semibold', filter === f ? 'bg-primary text-white' : 'hover:bg-ink/5')}
            >
              {f === 'all' ? 'Todas' : TYPE_LABEL[f] + 's'}
            </button>
          ))}
        </div>
      </div>
      {isLoading ? (
        <CardGridSkeleton count={6} />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
          {danzas.map((d) => (
            <Link key={d.id} to={`/candelaria/danzas/${d.id}`} className="group flex flex-col">
              <div className="overflow-hidden rounded-[var(--radius-card)] bg-line">
                <img src={d.image_url || '/images/puno_dancers.webp'} alt="" loading="lazy" className="aspect-[4/3] w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]" />
              </div>
              <p className="mt-3 text-sm font-semibold text-primary">{TYPE_LABEL[d.type]}{d.region ? ` · ${d.region}` : ''}</p>
              <h3 className="text-xl font-bold group-hover:text-primary">{d.name}</h3>
              <p className="mt-1 line-clamp-2 text-ink-soft">{d.description}</p>
            </Link>
          ))}
        </div>
      )}
    </section>
  )
}

function GallerySection() {
  const { data, isLoading } = useGallery()
  const [open, setOpen] = useState<GalleryPhoto | null>(null)
  const byYear = useMemo(() => {
    const groups = new Map<number, GalleryPhoto[]>()
    for (const p of data ?? []) groups.set(p.year, [...(groups.get(p.year) ?? []), p])
    return [...groups.entries()].sort((a, b) => b[0] - a[0])
  }, [data])

  return (
    <section id="galeria" aria-labelledby="galeria-titulo" className="scroll-mt-20 bg-white py-20">
      <div className="container-page">
        <h2 id="galeria-titulo" className="mb-10 text-3xl font-extrabold sm:text-4xl">Galería por año</h2>
        {isLoading && <div className="skeleton h-64" />}
        {!isLoading && byYear.length === 0 && <p className="text-ink-soft">La galería se está armando. Pronto habrá fotos de cada edición.</p>}
        <div className="flex flex-col gap-12">
          {byYear.map(([year, photos]) => (
            <div key={year} className="grid gap-6 md:grid-cols-[120px_1fr]">
              <h3 className="font-display text-4xl font-extrabold text-primary md:sticky md:top-24 md:self-start">{year}</h3>
              <div className="columns-2 gap-4 sm:columns-3">
                {photos.map((p) => (
                  <button key={p.id} onClick={() => setOpen(p)} className="mb-4 block w-full overflow-hidden rounded-2xl" aria-label={`Ampliar: ${p.title}`}>
                    <img src={p.image_url} alt={p.title} loading="lazy" className="w-full object-cover transition-transform duration-500 hover:scale-[1.03]" />
                  </button>
                ))}
              </div>
            </div>
          ))}
        </div>
      </div>
      {open && (
        <div role="dialog" aria-modal="true" aria-label={open.title} className="fixed inset-0 z-[2500] flex items-center justify-center bg-lake-deep/95 p-4" onClick={() => setOpen(null)}>
          <button className="absolute right-4 top-4 rounded-full bg-white p-2" aria-label="Cerrar" onClick={() => setOpen(null)}>
            <X className="size-5" />
          </button>
          <figure className="max-h-full max-w-5xl" onClick={(e) => e.stopPropagation()}>
            <img src={open.image_url} alt={open.title} className="max-h-[80vh] rounded-2xl object-contain" />
            <figcaption className="mt-3 text-white">
              <strong>{open.title}</strong> · {open.year}
              {open.description && <p className="text-white/75">{open.description}</p>}
            </figcaption>
          </figure>
        </div>
      )}
    </section>
  )
}

function PostsSection() {
  const { data, isLoading } = useContentList<Candelaria>('candelaria', { per_page: 6 })
  const items = data?.data ?? []
  if (!isLoading && !items.length) return null
  return (
    <section aria-labelledby="publicaciones" className="container-page py-20">
      <h2 id="publicaciones" className="mb-8 text-3xl font-extrabold sm:text-4xl">Historias y publicaciones</h2>
      {isLoading ? (
        <CardGridSkeleton count={3} />
      ) : (
        <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
          {items.map((c) => <ContentCard key={c.id} item={c} type="candelaria" />)}
        </div>
      )}
    </section>
  )
}

/** Convierte enlaces de YouTube/Vimeo en URL embebible */
function embedUrl(url: string) {
  const yt = url.match(/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([\w-]{11})/)
  if (yt) return `https://www.youtube-nocookie.com/embed/${yt[1]}`
  const vimeo = url.match(/vimeo\.com\/(\d+)/)
  if (vimeo) return `https://player.vimeo.com/video/${vimeo[1]}`
  return null
}

export function DanzaDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { data: d, isLoading, error, refetch } = useQuery({
    queryKey: ['danza', id],
    queryFn: () => getData<Danza>(`/candelaria-danzas/${id}`),
  })
  useDocumentTitle(d?.name ?? 'Danza')
  if (isLoading) return <Spinner />
  if (!d) return <div className="container-page py-20"><ErrorState message={parseApiError(error).message} onRetry={() => refetch()} /></div>
  const video = d.video_url ? embedUrl(d.video_url) : null

  return (
    <article className="container-page py-8 sm:py-12">
      <button onClick={() => navigate(-1)} className="mb-6 inline-flex items-center gap-1 text-sm font-semibold text-ink-soft hover:text-ink">
        <ChevronLeft className="size-4" /> Volver
      </button>
      <div className="grid gap-10 lg:grid-cols-2">
        <img src={d.image_url || '/images/puno_dancers.webp'} alt={d.name} className="aspect-[4/5] w-full rounded-[var(--radius-card)] object-cover" />
        <div>
          <p className="font-semibold text-primary">Danza {TYPE_LABEL[d.type].toLowerCase()}</p>
          <h1 className="mt-2 text-5xl font-extrabold">{d.name}</h1>
          {d.region && <p className="mt-3 flex items-center gap-2 text-ink-soft"><MapPin className="size-4.5" /> {d.region}</p>}
          <p className="prose-measure mt-6 text-lg leading-relaxed">{d.description}</p>
          {d.characteristics && (
            <section className="mt-8">
              <h2 className="mb-2 text-2xl font-extrabold">Características</h2>
              <p className="prose-measure whitespace-pre-line text-ink-soft">{d.characteristics}</p>
            </section>
          )}
        </div>
      </div>
      {d.history && (
        <section className="mt-14">
          <h2 className="mb-3 text-3xl font-extrabold">Historia</h2>
          <p className="prose-measure whitespace-pre-line text-lg leading-relaxed text-ink-soft">{d.history}</p>
        </section>
      )}
      {d.video_url && (
        <section className="mt-14">
          <h2 className="mb-4 text-3xl font-extrabold">Video</h2>
          {video ? (
            <iframe
              src={video}
              title={`Video de ${d.name}`}
              className="aspect-video w-full max-w-4xl rounded-[var(--radius-card)]"
              allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
              loading="lazy"
            />
          ) : (
            <a href={d.video_url} target="_blank" rel="noreferrer" className="font-semibold text-primary">Ver el video en otra pestaña</a>
          )}
        </section>
      )}
    </article>
  )
}
