import { useMemo, useState } from 'react'
import { MapView, type MapPoint } from '../components/Map'
import { Checkbox } from '../components/ui/Field'
import { CONTENT, LOCALE_CATEGORIES, titleOf } from '../lib/content'
import { useContentList, useDocumentTitle } from '../lib/hooks'
import type { Evento, Experiencia, Locale } from '../lib/types'

const LAYERS = {
  locales: '#2f9e45',
  experiencias: '#5930e8',
  eventos: '#10295c',
} as const
type Layer = keyof typeof LAYERS

export default function MapPage() {
  useDocumentTitle('Mapa')
  const [visible, setVisible] = useState<Record<Layer, boolean>>({ locales: true, experiencias: true, eventos: true })
  const [category, setCategory] = useState('')

  const locales = useContentList<Locale>('locales', { per_page: 100, category: category || undefined })
  const experiencias = useContentList<Experiencia>('experiencias', { per_page: 100 })
  const eventos = useContentList<Evento>('eventos', { per_page: 100 })

  const points = useMemo(() => {
    const out: MapPoint[] = []
    const add = (layer: Layer, items: (Locale | Experiencia | Evento)[] | undefined, subtitle: (i: never) => string) => {
      if (!visible[layer]) return
      for (const item of items ?? []) {
        if (!item.latitude || !item.longitude) continue
        out.push({
          id: `${layer}-${item.id}`,
          lat: Number(item.latitude),
          lng: Number(item.longitude),
          title: titleOf(item as never),
          subtitle: subtitle(item as never),
          href: `${CONTENT[layer].path}/${item.id}`,
          color: LAYERS[layer],
        })
      }
    }
    add('locales', locales.data?.data, (l: Locale) => LOCALE_CATEGORIES[l.category] ?? '')
    add('experiencias', experiencias.data?.data, () => 'Experiencia')
    add('eventos', eventos.data?.data, () => 'Evento')
    return out
  }, [locales.data, experiencias.data, eventos.data, visible])

  return (
    <div className="container-page py-12 sm:py-16">
      <h1 className="t-h1">Mapa de Puno</h1>
      <p className="prose-measure mt-3 text-lg text-ink-soft">Locales, experiencias y eventos con ubicación. Toca un marcador para ver el detalle.</p>
      <div className="mt-8 grid gap-6 lg:grid-cols-[260px_1fr]">
        <fieldset className="flex flex-col gap-4 self-start card p-5">
          <legend className="sr-only">Capas del mapa</legend>
          {(Object.keys(LAYERS) as Layer[]).map((layer) => (
            <div key={layer} className="flex items-center gap-2.5">
              <span className="size-3 rounded-full" style={{ background: LAYERS[layer] }} aria-hidden />
              <Checkbox label={CONTENT[layer].plural} checked={visible[layer]} onChange={(e) => setVisible((v) => ({ ...v, [layer]: e.target.checked }))} />
            </div>
          ))}
          <div className="border-t border-line pt-4">
            <label htmlFor="map-cat" className="mb-1.5 block text-sm font-semibold">Tipo de local</label>
            <select id="map-cat" value={category} onChange={(e) => setCategory(e.target.value)} className="field-input">
              <option value="">Todos</option>
              {Object.entries(LOCALE_CATEGORIES).map(([v, t]) => <option key={v} value={v}>{t}</option>)}
            </select>
          </div>
          <p className="text-sm text-ink-soft" aria-live="polite">{points.length} lugares en el mapa</p>
        </fieldset>
        <MapView points={points} height="70vh" />
      </div>
    </div>
  )
}
