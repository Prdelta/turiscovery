import { clsx } from 'clsx'
import { useId } from 'react'

/*
  Identidad textil de Turiscovery.
  Los motivos se dibujan como en un telar: una cuadrícula de 8 × 8 puntadas.
  Cada tipo de contenido tiene su motivo (tocapu) y su color de tinte natural,
  inspirados en los tejidos de Taquile y del altiplano.
*/

export type Motif = 'olas' | 'chakana' | 'ojo' | 'llave' | 'estrella' | 'sol' | 'persona' | 'isla'

const GRIDS: Record<Motif, string[]> = {
  // Olas del lago: experiencias
  olas: ['1......1', '11....11', '.11..11.', '..1111..', '...11...', '........', '11111111', '........'],
  // Chakana (cruz escalonada): eventos
  chakana: ['..1111..', '..1..1..', '111..111', '1......1', '1......1', '111..111', '..1..1..', '..1111..'],
  // Ojo de rombo: promociones
  ojo: ['...11...', '..1111..', '.11..11.', '11.11.11', '11.11.11', '.11..11.', '..1111..', '...11...'],
  // Llave escalonada (casa): locales
  llave: ['11111111', '1......1', '1.1111.1', '1.1..1.1', '1.1.11.1', '1.1....1', '1.111111', '1.......'],
  // Estrella de ocho puntas: Candelaria
  estrella: ['1..11..1', '.1.11.1.', '..1111..', '11111111', '11111111', '..1111..', '.1.11.1.', '1..11..1'],
  // Inti: almanaque
  sol: ['...11...', '.1....1.', '..1111..', '1.1111.1', '1.1111.1', '..1111..', '.1....1.', '...11...'],
  // Figura humana: anfitriones
  persona: ['...11...', '...11...', '.111111.', '1.1111.1', '..1111..', '..1..1..', '..1..1..', '.11..11.'],
  // Isla flotante: lugares del lago
  isla: ['........', '...11...', '..1111..', '.111111.', '11111111', '........', '1.1.1.1.', '.1.1.1.1'],
}

const pathCache = new Map<Motif, string>()

/** Convierte la cuadrícula en un solo path (una puntada = un cuadrado de 1 × 1) */
export function motifPath(motif: Motif) {
  let d = pathCache.get(motif)
  if (!d) {
    d = GRIDS[motif]
      .flatMap((row, y) => [...row].map((c, x) => (c === '1' ? `M${x} ${y}h1v1h-1z` : '')))
      .join('')
    pathCache.set(motif, d)
  }
  return d
}

/** Colores de tintes naturales andinos */
export const DYES = {
  cochinilla: '#b3243b',
  anil: '#2b3a8c',
  qolle: '#d99a1e',
  chilca: '#4c7a3a',
  morado: '#5930e8',
  tierra: '#8a5a2b',
  solOscuro: '#a67c00',
  lago: '#10295c',
  lana: '#f6efe3',
  mint: '#74fa8a',
} as const

export type Dye = keyof typeof DYES

/** Un tocapu: motivo suelto dentro de su recuadro de color */
export function Tocapu({
  motif,
  dye = 'morado',
  size = 28,
  framed = true,
  className,
  title,
}: {
  motif: Motif
  dye?: Dye
  size?: number
  framed?: boolean
  className?: string
  title?: string
}) {
  return (
    <svg
      width={size}
      height={size}
      viewBox={framed ? '-1 -1 10 10' : '0 0 8 8'}
      shapeRendering="crispEdges"
      className={clsx('shrink-0', className)}
      role={title ? 'img' : undefined}
      aria-hidden={title ? undefined : true}
      aria-label={title}
    >
      {framed && <rect x="-1" y="-1" width="10" height="10" fill={DYES[dye]} />}
      <path d={motifPath(motif)} fill={framed ? DYES.lana : DYES[dye]} />
    </svg>
  )
}

/**
 * Faja tejida (como un chumpi): franja de color con un motivo repetido
 * y dos hilos de borde. Se usa como divisor y para marcar la categoría.
 */
export function WovenBand({
  motif,
  dye = 'morado',
  accent = 'lana',
  height = 18,
  className,
}: {
  motif: Motif | Motif[]
  dye?: Dye
  accent?: Dye
  height?: number
  className?: string
}) {
  const id = useId().replace(/:/g, '')
  const motifs = Array.isArray(motif) ? motif : [motif]
  // Cada motivo ocupa 8 puntadas de alto dentro de una franja de 12 (2 de borde arriba y abajo)
  const unit = height / 12
  const step = 11 // 8 de motivo + 3 de separación
  const tile = step * motifs.length

  return (
    <svg width="100%" height={height} className={clsx('block', className)} aria-hidden shapeRendering="crispEdges">
      <defs>
        <pattern id={`w${id}`} width={tile * unit} height={height} patternUnits="userSpaceOnUse">
          <g transform={`scale(${unit})`}>
            <rect width={tile} height="12" fill={DYES[dye]} />
            <rect width={tile} height="0.8" y="0.6" fill={DYES[accent]} />
            <rect width={tile} height="0.8" y="10.6" fill={DYES[accent]} />
            {motifs.map((m, i) => (
              <path key={m + i} d={motifPath(m)} transform={`translate(${i * step + 1.5} 2)`} fill={DYES[accent]} />
            ))}
          </g>
        </pattern>
      </defs>
      <rect width="100%" height={height} fill={`url(#w${id})`} />
    </svg>
  )
}

/** Faja multicolor del sitio (cabecera del pie de página y separadores grandes) */
export function SiteBand({ height = 22, className }: { height?: number; className?: string }) {
  return (
    <div className={clsx('flex', className)} aria-hidden>
      <WovenBand motif="olas" dye="anil" height={height} className="flex-1" />
      <WovenBand motif="chakana" dye="cochinilla" height={height} className="flex-1" />
      <WovenBand motif="ojo" dye="chilca" height={height} className="flex-1" />
      <WovenBand motif="llave" dye="tierra" height={height} className="flex-1" />
      <WovenBand motif="estrella" dye="morado" height={height} className="flex-1" />
      <WovenBand motif="persona" dye="qolle" height={height} className="flex-1" />
    </div>
  )
}

/**
 * Separador de sección: una fila de puntadas de 8px en el tinte de la sección que empieza.
 */
export function StitchRow({ dye = 'morado', className }: { dye?: Dye; className?: string }) {
  const id = useId().replace(/:/g, '')
  return (
    <svg width="100%" height="8" className={clsx('block', className)} aria-hidden shapeRendering="crispEdges">
      <defs>
        <pattern id={`s${id}`} width="12" height="8" patternUnits="userSpaceOnUse">
          {/* Rombo pequeño de 3 × 3 puntadas (2px cada una) */}
          <rect x="2" y="0" width="2" height="2" fill={DYES[dye]} />
          <rect x="0" y="2" width="6" height="2" fill={DYES[dye]} />
          <rect x="2" y="4" width="2" height="2" fill={DYES[dye]} />
        </pattern>
      </defs>
      <rect width="100%" height="8" fill={`url(#s${id})`} />
    </svg>
  )
}

/**
 * Faja de progreso: cada paso es un motivo; los hechos van en el tinte
 * y los pendientes en color línea. Sirve para el audio, el desfile y los pasos de reserva.
 */
export function ProgressBand({
  total,
  done,
  motif = 'olas',
  dye = 'morado',
  size = 16,
  label,
}: {
  total: number
  done: number
  motif?: Motif
  dye?: Dye
  size?: number
  /** Texto para lectores de pantalla, por ejemplo "Paso 2 de 3" */
  label?: string
}) {
  return (
    <div
      role="progressbar"
      aria-valuemin={0}
      aria-valuemax={total}
      aria-valuenow={done}
      aria-label={label ?? `${done} de ${total}`}
      className="flex flex-wrap gap-1"
    >
      {Array.from({ length: total }, (_, i) => (
        <svg key={i} width={size} height={size} viewBox="0 0 8 8" shapeRendering="crispEdges" aria-hidden>
          <path d={motifPath(motif)} fill={i < done ? DYES[dye] : '#d8deea'} />
        </svg>
      ))}
    </div>
  )
}

/**
 * Tira de motivos sobre tinta, cada uno en su color (versión clara de su tinte
 * cuando el original no contrasta con la tinta). Es la faja de presentación de la marca.
 */
const ON_INK: Record<Motif, string> = {
  olas: '#b6dfe7',
  chakana: '#e0405a',
  ojo: '#74fa8a',
  llave: '#d99a1e',
  estrella: '#7654ec',
  sol: '#f2b914',
  persona: '#ffffff',
  isla: '#b6dfe7',
}

export function MotifStrip({ motifs, size = 28, className }: { motifs: Motif[]; size?: number; className?: string }) {
  return (
    <div className={clsx('flex items-center gap-3 overflow-hidden rounded-[var(--radius-control)] bg-ink px-4 py-3', className)} aria-hidden>
      {motifs.map((m, i) => (
        <svg key={m + i} width={size} height={size} viewBox="0 0 8 8" shapeRendering="crispEdges" className="shrink-0">
          <path d={motifPath(m)} fill={ON_INK[m]} />
        </svg>
      ))}
    </div>
  )
}
