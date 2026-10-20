import { useQuery } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { FileUp } from 'lucide-react'
import { lazy, Suspense, useEffect, useRef, useState } from 'react'
import { Link } from 'react-router'
import { Spinner } from '../components/ui/Feedback'
import { useDocumentTitle } from '../lib/hooks'
import { webglAvailable } from '../three/support'

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

  if (!canRender) {
    return (
      <div className="container-page py-20">
        <h1 className="t-h1">Puno en 3D</h1>
        <p className="mt-4 text-ink-soft">Tu navegador no permite gráficos 3D (WebGL). Prueba con una versión reciente de Chrome, Edge, Firefox o Safari.</p>
      </div>
    )
  }

  return (
    <>
      <section className="bg-lake-deep text-white">
        <div className="container-page py-12 sm:py-16">
          <h1 className="font-display text-[clamp(2.4rem,6vw,4.2rem)] font-extrabold leading-none text-white">Puno en 3D</h1>
          <p className="prose-measure mt-4 text-lg text-white/85">
            Piezas y lugares para mirar de cerca. Para recorrer el lago completo,{' '}
            <Link to="/" className="font-semibold text-mint underline-offset-4 hover:underline">vuelve a la portada</Link>.
          </p>
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
      <h2 id="modelos" className="t-h2">Modelos de Puno</h2>
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
                'rounded-[var(--radius-card)] border-2 p-4 text-left transition-colors',
                !localFile && activeId === m.id ? 'border-ink bg-sky' : 'border-line bg-white hover:border-ink',
              )}
            >
              <p className="font-bold">{m.title}</p>
              <p className="mt-1 text-sm text-ink-soft">{m.description}</p>
            </button>
          ))}
          <button
            onClick={() => input.current?.click()}
            className="flex items-center gap-3 rounded-[var(--radius-card)] border-2 border-dashed border-ink-soft bg-white p-4 text-left text-sm hover:border-ink"
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
          <div className="h-[520px] overflow-hidden rounded-[var(--radius-card)] border-2 border-ink">
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
