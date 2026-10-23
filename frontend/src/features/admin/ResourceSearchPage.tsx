import { useQuery } from '@tanstack/react-query'
import { ExternalLink } from 'lucide-react'
import { useState } from 'react'
import { PageHeader } from '@/shared/ui/PageHeader'
import { Button } from '@/shared/ui/Button'
import { PanelCard } from '@/shared/ui/Panel'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { adminQueries } from '@/features/admin/api'

export function ResourceSearchPage() {
  useDocumentTitle('Buscar recursos')
  const [query, setQuery] = useState('')
  const [submitted, setSubmitted] = useState('')
  const wiki = useQuery(adminQueries.wiki(submitted))
  const images = useQuery(adminQueries.images(submitted))

  return (
    <>
      <PageHeader title="Buscar recursos" description="Consulta Wikipedia y Wikimedia Commons para documentar danzas y fotos. Revisa siempre la licencia antes de usar una imagen." />
      <form onSubmit={(e) => { e.preventDefault(); setSubmitted(query.trim()) }} className="mb-8 flex max-w-xl gap-2">
        <label htmlFor="res-q" className="sr-only">Tema</label>
        <input id="res-q" value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Ejemplo: Diablada puneña" className="field-input" minLength={3} />
        <Button type="submit">Buscar</Button>
      </form>
      {submitted && (
        <div className="grid gap-6 xl:grid-cols-2">
          <PanelCard title="Wikipedia">
            {wiki.isLoading ? <div className="skeleton h-32" /> : wiki.data ? (
              <div className="flex gap-4">
                {wiki.data.thumbnail && <img src={wiki.data.thumbnail} alt="" className="size-24 rounded-[var(--radius-control)] border-2 border-ink object-cover" />}
                <div>
                  <p className="font-bold">{wiki.data.title}</p>
                  {wiki.data.description && <p className="text-sm text-ink-soft">{wiki.data.description}</p>}
                  <p className="mt-2 text-sm">{wiki.data.extract}</p>
                  {wiki.data.url && <a href={wiki.data.url} target="_blank" rel="noreferrer" className="mt-2 inline-flex items-center gap-1 text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">Abrir artículo <ExternalLink className="size-3.5" /></a>}
                </div>
              </div>
            ) : <p className="text-ink-soft">No hay un artículo con ese título exacto. Prueba con el nombre completo.</p>}
          </PanelCard>
          <PanelCard title="Wikimedia Commons">
            {images.isLoading ? <div className="skeleton h-32" /> : images.data?.wikimedia.length ? (
              <ul className="flex flex-col divide-y-2 divide-line">
                {images.data.wikimedia.map((r) => (
                  <li key={r.page_url} className="py-2.5">
                    <a href={r.page_url} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1 font-bold text-primary-dark underline decoration-2 underline-offset-4">{r.title.replace(/^File:|^Archivo:/, '')} <ExternalLink className="size-3.5" /></a>
                    {r.snippet && <p className="line-clamp-2 text-sm text-ink-soft">{r.snippet}</p>}
                  </li>
                ))}
              </ul>
            ) : <p className="text-ink-soft">Sin archivos para esa búsqueda.</p>}
          </PanelCard>
        </div>
      )}
    </>
  )
}
