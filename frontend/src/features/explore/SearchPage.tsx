import { useQueries } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { Search } from 'lucide-react'
import { Link, useSearchParams } from 'react-router'
import { catalogQueries } from '@/features/catalog/api'
import { ContentCard } from '@/features/catalog/components/ContentCard'
import { CardGridSkeleton, EmptyState } from '@/shared/ui/Feedback'
import { CONTENT, type ContentType } from '@/shared/config/content'
import { useDebounced, useDocumentTitle } from '@/shared/lib/hooks'

const TYPES: ContentType[] = ['experiencias', 'eventos', 'promociones', 'locales', 'candelaria']

export default function SearchPage() {
  useDocumentTitle('Buscar')
  const [params, setParams] = useSearchParams()
  const q = params.get('q') ?? ''
  const only = params.get('tipo') as ContentType | null
  const debounced = useDebounced(q.trim())
  const types = only ? [only] : TYPES

  const results = useQueries({
    // Misma clave que los listados: al editar contenido, la búsqueda también se refresca
    queries: types.map((type) => ({
      ...catalogQueries.list(type, { search: debounced, per_page: only ? 24 : 6 }),
      enabled: debounced.length >= 2,
    })),
  })

  const set = (key: string, value: string | null) => {
    const next = new URLSearchParams(params)
    if (value) next.set(key, value)
    else next.delete(key)
    setParams(next, { replace: true })
  }

  const loading = results.some((r) => r.isLoading)
  const total = results.reduce((s, r) => s + (r.data?.total ?? 0), 0)

  return (
    <div className="container-page py-12 sm:py-16">
      <h1 className="t-h1">Buscar en Turiscovery</h1>
      <div role="search" className="mt-8 flex flex-col gap-4">
        <div className="relative max-w-2xl">
          <label htmlFor="search-q" className="sr-only">Qué buscas</label>
          <Search className="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-ink-soft" aria-hidden />
          <input
            id="search-q"
            autoFocus
            value={q}
            onChange={(e) => set('q', e.target.value)}
            placeholder="Ejemplo: Uros, kayak, diablada, pizzería"
            className="field-input h-14 pl-12 text-lg"
          />
        </div>
        <div role="group" aria-label="Tipo de contenido" className="flex flex-wrap gap-2">
          <Chip active={!only} onClick={() => set('tipo', null)}>Todo</Chip>
          {TYPES.map((t) => (
            <Chip key={t} active={only === t} onClick={() => set('tipo', t)}>{CONTENT[t].plural}</Chip>
          ))}
        </div>
      </div>

      <div className="mt-12">
        {debounced.length < 2 ? (
          <p className="text-ink-soft">Escribe al menos dos letras para buscar en experiencias, eventos, promociones, locales y Candelaria.</p>
        ) : loading ? (
          <CardGridSkeleton count={3} />
        ) : total === 0 ? (
          <EmptyState title={`Sin resultados para “${debounced}”`}>Revisa la ortografía o prueba con una palabra más general.</EmptyState>
        ) : (
          <div className="flex flex-col gap-14">
            {types.map((type, i) => {
              const data = results[i].data
              if (!data?.data.length) return null
              return (
                <section key={type} aria-labelledby={`r-${type}`}>
                  <div className="mb-5 flex items-baseline justify-between gap-4">
                    <h2 id={`r-${type}`} className="t-h2">
                      {CONTENT[type].plural} <span className="text-base font-medium text-ink-soft">({data.total})</span>
                    </h2>
                    {!only && data.total > data.data.length && (
                      <Link to={`/buscar?q=${encodeURIComponent(debounced)}&tipo=${type}`} className="text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">
                        Ver los {data.total}
                      </Link>
                    )}
                  </div>
                  <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                    {data.data.map((item) => <ContentCard key={item.id} item={item} type={type} />)}
                  </div>
                </section>
              )
            })}
          </div>
        )}
      </div>
    </div>
  )
}

function Chip({ active, onClick, children }: { active: boolean; onClick: () => void; children: React.ReactNode }) {
  return (
    <button
      onClick={onClick}
      aria-pressed={active}
      className={clsx('inline-flex min-h-11 items-center gap-1.5 whitespace-nowrap rounded-full border-2 border-ink px-4 text-sm font-bold transition-colors', active ? 'bg-ink text-white' : 'bg-white text-ink hover:bg-sky')}
    >
      {children}
    </button>
  )
}
