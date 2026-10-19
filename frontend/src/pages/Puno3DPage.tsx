import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { FileUp, MousePointer2 } from 'lucide-react'
import { lazy, Suspense, useEffect, useRef, useState } from 'react'
import { Spinner } from '../components/ui/Feedback'
import { useDocumentTitle } from '../lib/hooks'
import { LAKE_HOTSPOTS, webglAvailable, type Hotspot } from '../three/support'

const LakeScene = lazy(() => import('../three/LakeScene'))
const ModelViewer = lazy(() => import('../three/ModelViewer'))

interface ModelEntry {
  id: string
  title: string
  description: string
  file: string
  credit?: string
}

export default function Puno3DPage() {
  useDocumentTitle('Puno en 3D')
  const [canRender] = useState(() => webglAvailable())
  const [active, setActive] = useState<Hotspot | null>(null)

  if (!canRender) {
    return (
      <div className="container-page py-20">
        <h1 className="text-4xl font-extrabold">Puno en 3D</h1>
        <p className="mt-4 text-ink-soft">Tu navegador no permite gráficos 3D (WebGL). Prueba con una versión reciente de Chrome, Edge, Firefox o Safari.</p>
      </div>
    )
  }

  return (
    <>
      <section aria-labelledby="lago-3d" className="relative h-[78svh] min-h-[520px] bg-lake">
        <Suspense fallback={<Spinner label="Preparando el lago…" />}>
          <LakeScene interactive hotspots={LAKE_HOTSPOTS} activeHotspot={active?.label} onHotspot={setActive} />
        </Suspense>
        <div className="pointer-events-none absolute inset-x-0 top-0">
          <div className="container-page pt-8">
            <div className="pointer-events-auto max-w-md rounded-[var(--radius-card)] bg-white/95 p-5 shadow-sm">
              <h1 id="lago-3d" className="text-3xl font-extrabold">Lago Titicaca en 3D</h1>
              {active ? (
                <>
                  <h2 className="mt-3 text-lg font-bold text-primary">{active.label}</h2>
                  <p className="mt-1 text-ink-soft">{active.description}</p>
                  <button onClick={() => setActive(null)} className="pointer-events-auto mt-3 text-sm font-semibold text-primary">Seguir explorando</button>
                </>
              ) : (
                <p className="mt-2 flex items-start gap-2 text-ink-soft">
                  <MousePointer2 className="mt-1 size-4 shrink-0" aria-hidden />
                  Arrastra para girar, usa la rueda o pellizca para acercarte y toca las etiquetas para saber más.
                </p>
              )}
            </div>
          </div>
        </div>
      </section>
      <ModelsSection />
    </>
  )
}

function ModelsSection() {
  const { data: models = [], isLoading } = useQuery({
    queryKey: ['models-manifest'],
    queryFn: async () => {
      const res = await fetch('/models/manifest.json')
      if (!res.ok) return []
      return (await res.json()) as ModelEntry[]
    },
    staleTime: Infinity,
  })
  const [selected, setSelected] = useState<string | null>(null)
  const [localFile, setLocalFile] = useState<{ name: string; url: string } | null>(null)
  const input = useRef<HTMLInputElement>(null)

  useEffect(() => () => {
    if (localFile) URL.revokeObjectURL(localFile.url)
  }, [localFile])

  const activeId = selected ?? models[0]?.id ?? null
  const current = models.find((m) => m.id === activeId)
  const url = localFile?.url ?? (current ? `/models/${current.file}` : null)

  return (
    <section aria-labelledby="modelos" className="container-page py-20">
      <h2 id="modelos" className="text-3xl font-extrabold sm:text-4xl">Modelos de Puno</h2>
      <p className="prose-measure mt-2 text-ink-soft">
        Piezas y lugares en tres dimensiones. Gíralos con el ratón o con el dedo.
      </p>
      <div className="mt-8 grid gap-6 lg:grid-cols-[300px_1fr]">
        <div className="flex flex-col gap-2">
          {isLoading && <div className="skeleton h-20" />}
          {models.map((m) => (
            <button
              key={m.id}
              onClick={() => {
                setLocalFile(null)
                setSelected(m.id)
              }}
              aria-pressed={!localFile && activeId === m.id}
              className={clsx(
                'rounded-2xl border p-4 text-left transition-colors',
                !localFile && activeId === m.id ? 'border-primary bg-primary/5' : 'border-line bg-white hover:border-ink/40',
              )}
            >
              <p className="font-bold">{m.title}</p>
              <p className="mt-1 text-sm text-ink-soft">{m.description}</p>
            </button>
          ))}
          <button
            onClick={() => input.current?.click()}
            className="flex items-center gap-3 rounded-2xl border-2 border-dashed border-line p-4 text-left text-sm hover:border-primary"
          >
            <FileUp className="size-5 shrink-0 text-primary" aria-hidden />
            <span>
              <strong className="block">Abrir un modelo de tu equipo</strong>
              <span className="text-ink-soft">Archivo .glb para previsualizarlo aquí. No se sube a ningún servidor.</span>
            </span>
          </button>
          <input
            ref={input}
            type="file"
            accept=".glb,.gltf,model/gltf-binary"
            hidden
            onChange={(e) => {
              const file = e.target.files?.[0]
              if (file) setLocalFile({ name: file.name, url: URL.createObjectURL(file) })
              e.target.value = ''
            }}
          />
        </div>
        <div className="flex flex-col gap-3">
          <div className="h-[520px] overflow-hidden rounded-[var(--radius-card)] border border-line">
            {url ? (
              <Suspense fallback={<Spinner label="Cargando visor…" />}>
                <ModelViewer url={url} />
              </Suspense>
            ) : (
              <div className="flex h-full items-center justify-center text-ink-soft">Elige un modelo para verlo.</div>
            )}
          </div>
          <p className="text-sm text-ink-soft">
            {localFile ? `Archivo local: ${localFile.name}` : current?.credit ?? ''}
          </p>
        </div>
      </div>
    </section>
  )
}
