/* Utilidades ligeras del 3D: no importan three.js, para no cargarlo en el bundle principal */

export function webglAvailable() {
  try {
    const canvas = document.createElement('canvas')
    return !!(canvas.getContext('webgl2') || canvas.getContext('webgl'))
  } catch {
    return false
  }
}
