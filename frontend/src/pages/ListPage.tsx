import { Search } from 'lucide-react'
import { useSearchParams } from 'react-router'
import { ContentCard } from '../components/ContentCard'
import { Tocapu, WovenBand } from '../components/textile/Weave'
import { CardGridSkeleton, EmptyState, ErrorState, Pagination } from '../components/ui/Feedback'
import { CONTENT, DIFFICULTY, DISCOUNT_TYPES, EVENT_CATEGORIES, LOCALE_CATEGORIES, type ContentType } from '../lib/content'
import { useContentList, useDebounced, useDocumentTitle } from '../lib/hooks'
import { parseApiError } from '../lib/api'

interface Filter {
  param: string
  label: string
  options: Record<string, string>
}

const PAGE_INFO: Partial<Record<ContentType, { title: string; text: string; filters: Filter[] }>> = {
  experiencias: {
    title: 'Experiencias en Puno',
    text: 'Tours al lago, caminatas, talleres y gastronomía, organizados por agencias y guías locales.',
    filters: [{ param: 'difficulty', label: 'Dificultad', options: DIFFICULTY }],
  },
  eventos: {
    title: 'Eventos',
    text: 'Conciertos, festivales y actividades culturales que aún no terminan.',
    filters: [{ param: 'category', label: 'Categoría', options: EVENT_CATEGORIES }],
  },
  promociones: {
    title: 'Promociones',
    text: 'Descuentos vigentes en locales de Puno. Guarda el cupón y preséntalo al pagar.',
    filters: [{ param: 'type', label: 'Tipo de descuento', options: DISCOUNT_TYPES }],
  },
  locales: {
    title: 'Locales',
    text: 'Restaurantes, hoteles, agencias, museos y tiendas de artesanía asociados a Turiscovery.',
    filters: [{ param: 'category', label: 'Categoría', options: LOCALE_CATEGORIES }],
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
  if (type === 'experiencias') query.max_price = params.get('max_price') || undefined

  const { data, isLoading, isError, error, refetch, isFetching } = useContentList(type, query)

  const update = (key: string, value: string) => {
    const next = new URLSearchParams(params)
    if (value) next.set(key, value)
    else next.delete(key)
    if (key !== 'page') next.delete('page')
    setParams(next, { replace: key !== 'page' })
  }

  const hasFilters = !!search || info.filters.some((f) => params.get(f.param)) || !!params.get('max_price')

  return (
    <div className="container-page py-12 sm:py-16">
      <header className="mb-10">
        <div className="flex items-center gap-4">
          <Tocapu motif={CONTENT[type].motif} dye={CONTENT[type].dye} size={48} />
          <h1 className="text-4xl font-extrabold sm:text-5xl">{info.title}</h1>
        </div>
        <p className="prose-measure mt-4 text-lg text-ink-soft">{info.text}</p>
        <WovenBand motif={CONTENT[type].motif} dye={CONTENT[type].dye} height={12} className="mt-6 rounded-sm" />
      </header>

      <div className="mb-10 flex flex-wrap items-end gap-3" role="search">
        <div className="relative min-w-60 flex-1">
          <label htmlFor="list-search" className="sr-only">Buscar {CONTENT[type].plural.toLowerCase()}</label>
          <Search className="pointer-events-none absolute left-3.5 top-1/2 size-4.5 -translate-y-1/2 text-ink-soft" aria-hidden />
          <input
            id="list-search"
            value={search}
            onChange={(e) => update('q', e.target.value)}
            placeholder={`Buscar ${CONTENT[type].plural.toLowerCase()}`}
            className="field-input pl-10"
          />
        </div>
        {info.filters.map((f) => (
          <div key={f.param} className="min-w-44">
            <label htmlFor={`f-${f.param}`} className="sr-only">{f.label}</label>
            <select id={`f-${f.param}`} value={params.get(f.param) ?? ''} onChange={(e) => update(f.param, e.target.value)} className="field-input">
              <option value="">{f.label}: todas</option>
              {Object.entries(f.options).map(([v, t]) => <option key={v} value={v}>{t}</option>)}
            </select>
          </div>
        ))}
        {type === 'experiencias' && (
          <div className="min-w-44">
            <label htmlFor="f-price" className="sr-only">Precio máximo</label>
            <select id="f-price" value={params.get('max_price') ?? ''} onChange={(e) => update('max_price', e.target.value)} className="field-input">
              <option value="">Cualquier precio</option>
              <option value="50">Hasta S/ 50</option>
              <option value="100">Hasta S/ 100</option>
              <option value="200">Hasta S/ 200</option>
              <option value="500">Hasta S/ 500</option>
            </select>
          </div>
        )}
        {hasFilters && (
          <button onClick={() => setParams({})} className="h-11 px-2 text-sm font-semibold text-primary">
            Quitar filtros
          </button>
        )}
      </div>

      {isLoading ? (
        <CardGridSkeleton />
      ) : isError ? (
        <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
      ) : data && data.data.length > 0 ? (
        <>
          <p className="mb-5 text-sm text-ink-soft" aria-live="polite">
            {data.total} resultado{data.total === 1 ? '' : 's'}
            {isFetching && ' · actualizando…'}
          </p>
          <div className="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            {data.data.map((item) => <ContentCard key={item.id} item={item} type={type} />)}
          </div>
          <Pagination page={data.current_page} lastPage={data.last_page} onChange={(p) => update('page', String(p))} />
        </>
      ) : (
        <EmptyState title={hasFilters ? 'Nada coincide con tu búsqueda' : `Todavía no hay ${CONTENT[type].plural.toLowerCase()} publicadas`}>
          {hasFilters ? 'Prueba con otras palabras o quita algún filtro.' : 'Los socios de Turiscovery publican contenido nuevo cada semana.'}
        </EmptyState>
      )}
    </div>
  )
}
