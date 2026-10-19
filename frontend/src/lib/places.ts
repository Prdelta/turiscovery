import type { Dye, Motif } from '../components/textile/Weave'

/*
  Lugares del lago y alrededores. Cada uno tiene:
  - coordenadas reales, para ubicar el contenido publicado por los socios;
  - posición en la escena 3D (estilizada, no a escala), para navegar por el lago.
  No importa three.js para que la portada cargue rápido.
*/

export interface Place {
  id: string
  name: string
  short: string
  description: string
  lat: number
  lng: number
  /** Radio en km para considerar que un contenido está en este lugar */
  radiusKm: number
  /** Posición en la escena 3D (x, y, z) */
  scene: [number, number, number]
  motif: Motif
  dye: Dye
}

export const PLACES: Place[] = [
  {
    id: 'puno',
    name: 'Puno',
    short: 'La ciudad',
    description: 'La capital del folclore peruano, a orillas de la bahía. Mercados, la catedral y el puerto de donde salen los botes.',
    lat: -15.8402,
    lng: -70.0219,
    radiusKm: 5,
    scene: [-124, 0, 22],
    motif: 'llave',
    dye: 'tierra',
  },
  {
    id: 'uros',
    name: 'Islas de los Uros',
    short: 'Islas flotantes',
    description: 'Islas hechas a mano con capas de totora, donde viven familias del pueblo uro. Se llega en bote desde el puerto de Puno.',
    lat: -15.818,
    lng: -69.97,
    radiusKm: 5,
    scene: [-82, 0, 10],
    motif: 'isla',
    dye: 'qolle',
  },
  {
    id: 'taquile',
    name: 'Isla Taquile',
    short: 'Tejidos y terrazas',
    description: 'Isla de terrazas de cultivo cuyo arte textil es Patrimonio Inmaterial de la Humanidad. Aquí los hombres tejen sus propios chullos.',
    lat: -15.77,
    lng: -69.685,
    radiusKm: 5,
    scene: [40, 0, -18],
    motif: 'olas',
    dye: 'anil',
  },
  {
    id: 'amantani',
    name: 'Isla Amantaní',
    short: 'Vivir con una familia',
    description: 'La isla más grande del lado peruano del lago. Sus familias reciben a los viajeros en casa para pasar la noche.',
    lat: -15.66,
    lng: -69.715,
    radiusKm: 6,
    scene: [14, 0, -78],
    motif: 'persona',
    dye: 'cochinilla',
  },
  {
    id: 'sillustani',
    name: 'Sillustani',
    short: 'Torres funerarias',
    description: 'Chullpas preincas, torres funerarias más anchas arriba que abajo, junto a la laguna Umayo.',
    lat: -15.722,
    lng: -70.155,
    radiusKm: 5,
    scene: [-150, 0, -92],
    motif: 'chakana',
    dye: 'tierra',
  },
  {
    id: 'chucuito',
    name: 'Chucuito',
    short: 'Pueblo colonial',
    description: 'Pueblo de la orilla sur con iglesias coloniales y el Inca Uyo, conocido como el templo de la fertilidad.',
    lat: -15.894,
    lng: -69.891,
    radiusKm: 5,
    scene: [-58, 0, 108],
    motif: 'sol',
    dye: 'morado',
  },
]

export function placeById(id?: string | null) {
  return PLACES.find((p) => p.id === id)
}

function haversineKm(lat1: number, lng1: number, lat2: number, lng2: number) {
  const R = 6371
  const toRad = (d: number) => (d * Math.PI) / 180
  const dLat = toRad(lat2 - lat1)
  const dLng = toRad(lng2 - lng1)
  const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2
  return 2 * R * Math.asin(Math.sqrt(a))
}

/** Lugar más cercano a unas coordenadas, si cae dentro de su radio */
export function placeFor(lat?: number | string | null, lng?: number | string | null): Place | undefined {
  const la = Number(lat)
  const ln = Number(lng)
  if (!la || !ln) return undefined
  let best: Place | undefined
  let bestKm = Infinity
  for (const p of PLACES) {
    const km = haversineKm(la, ln, p.lat, p.lng)
    if (km <= p.radiusKm && km < bestKm) {
      best = p
      bestKm = km
    }
  }
  return best
}
