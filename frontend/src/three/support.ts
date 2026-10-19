/* Utilidades ligeras del 3D: no importan three.js, para no cargarlo en el bundle principal */

export interface Hotspot {
  label: string
  description: string
  position: [number, number, number]
}

export const LAKE_HOTSPOTS: Hotspot[] = [
  { label: 'Islas de los Uros', description: 'Islas flotantes hechas a mano con totora, a 5 km del puerto de Puno.', position: [-6, 5, 6] },
  { label: 'Isla Taquile', description: 'Terrazas de cultivo y textiles declarados Patrimonio Inmaterial por la UNESCO.', position: [62, 12, -48] },
  { label: 'Cordillera Real', description: 'Nevados de más de 6 000 m que se reflejan en el lago al amanecer.', position: [60, 70, -360] },
]

export function webglAvailable() {
  try {
    const canvas = document.createElement('canvas')
    return !!(canvas.getContext('webgl2') || canvas.getContext('webgl'))
  } catch {
    return false
  }
}
