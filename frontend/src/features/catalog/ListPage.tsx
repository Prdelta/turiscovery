import { Search, X } from 'lucide-react'
import { useSearchParams } from 'react-router'
import { ContentCard } from '@/features/catalog/components/ContentCard'
import { CategoryNavBand } from '@/shared/textile/Tags'
import { Tocapu } from '@/shared/textile/Weave'
import { Button } from '@/shared/ui/Button'
import { CardGridSkeleton, EmptyState, ErrorState, FilterChip, Pagination } from '@/shared/ui/Feedback'
import { CONTENT, DIFFICULTY, DISCOUNT_TYPES, EVENT_CATEGORIES, LOCALE_CATEGORIES, type ContentType } from '@/shared/config/content'
import { useDebounced, useDocumentTitle } from '@/shared/lib/hooks'
import { useContentList } from '@/features/catalog/api'
import { parseApiError } from '@/shared/api/client'

interface Filter {
  param: string
  label: string
  /** Texto del chip que quita el filtro */
  all: string
  options: Record<string, string>
}

const PRICES: Record<string, string> = { '50': 'Hasta S/ 50', '100': 'Hasta S/ 100', '200': 'Hasta S/ 200', '500': 'Hasta S/ 500' }

const PAGE_INFO: Partial<Record<ContentType, { title: string; text: string; filters: Filter[] }>> = {
  experiencias: {
    title: 'Experiencias en Puno',
    text: 'Tours al lago, caminatas, talleres y gastronomía, organizados por agencias y guías locales.',
    filters: [
      { param: 'difficulty', label: 'Dificultad', all: 'Cualquier dificultad', options: DIFFICULTY },
      { param: 'max_price', label: 'Precio', all: 'Cualquier precio', options: PRICES },
    ],
  },
  eventos: {
    title: 'Eventos',
    text: 'Conciertos, festivales y actividades culturales que aún no terminan.',
    filters: [{ param: 'category', label: 'Categoría', all: 'Todos', options: EVENT_CATEGORIES }],
  },
  promociones: {
    title: 'Promociones',
    text: 'Descuentos vigentes en locales de Puno. Guarda el cupón y preséntalo al pagar.',
    filters: [{ param: 'type', label: 'Tipo de descuento', all: 'Todas', options: DISCOUNT_TYPES }],
  },
  locales: {
    title: 'Locales',
    text: 'Restaurantes, hoteles, agencias, museos y tiendas de artesanía asociados a Ñan Puno.',
    filters: [{ param: 'category', label: 'Categoría', all: 'Todos', options: LOCALE_CATEGORIES }],
  },
}

export default function ListPage({ type }: { type: ContentType }) {
  const info = PAGE_INFO[type]!
  useDocumentTitle(CONTENT[type].plural)
  const [params, setParams] = useSearchParams()
  const page = Number(params.get('page') ?? 1)
  const search = params.get('q') ?? ''
  const debounced = useDebounced(search)

  const query: Record<string, unknown> = { page, per_page: 12, search: debounced || undefined }
  for (const f of info.filters) query[f.param] = params.get(f.param) || undefined

  const { data, isLoading, isError, error, refetch, isFetching } = useContentList(type, query)

  const update = (key: string, value: string) => {
    const next = new URLSearchParams(params)
    if (value) next.set(key, value)
    else next.delete(key)
    if (key !== 'page') next.delete('page')
    setParams(next, { replace: key !== 'page', preventScrollReset: key !== 'page' })
  }

  const activeFilters = info.filters.filter((f) => params.get(f.param)).length
  const hasFilters = !!search || activeFilters > 0
  const clear = () => setParams({}, { replace: true, preventScrollReset: true })

  return (
    <>
      <CategoryNavBand />
      <div className="container-page py-8 sm:py-12">
        <header className="mb-6 flex items-start gap-4">
          <Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={48} className="shrink-0" />
          <div>
            <h1 className="t-h1">{info.title}</h1>
            <p className="prose-measure mt-2 text-ink-soft">{info.text}</p>
          </div>
        </header>

        <div role="search" className="mb-8 flex flex-col gap-4">
          <div className="relative">
            <label htmlFor="list-search" className="sr-only">Buscar {CONTENT[type].plural.toLowerCase()}</label>
            <Search className="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-ink-soft" aria-hidden />
            <input
              id="list-search"
              type="search"
              value={search}
              onChange={(e) => update('q', e.target.value)}
              placeholder={`Buscar ${CONTENT[type].plural.toLowerCase()}`}
              className="field-input pl-12"
            />
          </div>

          {info.filters.map((f) => {
            const current = params.get(f.param) ?? ''
            return (
              <div key={f.param} role="group" aria-labelledby={`f-${f.param}`}>
                <p id={`f-${f.param}`} className="t-label mb-2 text-ink-soft">{f.label}</p>
                <div className="-mx-4 overflow-x-auto px-4 pb-1">
                  <div className="flex w-max gap-2">
                    <FilterChip selected={!current} onClick={() => update(f.param, '')}>{f.all}</FilterChip>
                    {Object.entries(f.options).map(([v, t]) => (
                      <FilterChip key={v} selected={current === v} onClick={() => update(f.param, current === v ? '' : v)}>
                        {t}
                      </FilterChip>
                    ))}
                  </div>
                </div>
              </div>
            )
          })}
        </div>

        <div className="mb-5 flex min-h-11 flex-wrap items-center justify-between gap-3">
          <p className="text-sm text-ink-soft" aria-live="polite">
            {data ? `${data.total} resultado${data.total === 1 ? '' : 's'}` : ''}
            {isFetching && !isLoading && ' · actualizando…'}
          </p>
          {hasFilters && (
            <Button variant="ghost" size="sm" icon={<X className="size-4" />} onClick={clear}>
              Quitar filtros{activeFilters ? ` (${activeFilters})` : ''}
            </Button>
          )}
        </div>

        {isLoading ? (
          <CardGridSkeleton />
        ) : isError ? (
          <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
        ) : data && data.data.length > 0 ? (
          <>
            <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              {data.data.map((item) => <ContentCard key={item.id} item={item} type={type} />)}
            </div>
            <Pagination page={data.current_page} lastPage={data.last_page} onChange={(p) => update('page', String(p))} />
          </>
        ) : (
          <EmptyState
            title={hasFilters ? 'Nada coincide con tu búsqueda' : `Todavía no hay ${CONTENT[type].plural.toLowerCase()} publicadas`}
            icon={<Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={40} framed={false} />}
            action={hasFilters ? <Button variant="secondary" onClick={clear}>Quitar filtros</Button> : undefined}
          >
            {hasFilters ? 'Prueba con otras palabras o quita algún filtro.' : 'Los socios de Ñan Puno publican contenido nuevo cada semana.'}
          </EmptyState>
        )}
      </div>
    </>
  )
}
