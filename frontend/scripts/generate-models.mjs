/*
  Genera los modelos .glb de ejemplo de /public/models a partir de geometría procedural.
  Uso: node scripts/generate-models.mjs
  Sirven como punto de partida: se pueden reemplazar por modelos fotogramétricos reales
  manteniendo el mismo nombre de archivo o añadiendo entradas en manifest.json.
*/
import { mkdirSync, writeFileSync } from 'node:fs'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import * as THREE from 'three'
import { GLTFExporter } from 'three/examples/jsm/exporters/GLTFExporter.js'

// GLTFExporter usa FileReader del navegador para el formato binario
globalThis.FileReader ??= class {
  readAsArrayBuffer(blob) {
    blob.arrayBuffer().then((buf) => {
      this.result = buf
      this.onloadend?.()
      this.onload?.({ target: this })
    })
  }
  readAsDataURL(blob) {
    blob.arrayBuffer().then((buf) => {
      this.result = `data:${blob.type};base64,${Buffer.from(buf).toString('base64')}`
      this.onloadend?.()
      this.onload?.({ target: this })
    })
  }
}

const outDir = join(dirname(fileURLToPath(import.meta.url)), '..', 'public', 'models')
mkdirSync(outDir, { recursive: true })

const mat = (color, roughness = 0.95) => new THREE.MeshStandardMaterial({ color, roughness, flatShading: true })

function rand(seed) {
  let s = seed
  return () => ((s = (s * 16807) % 2147483647) - 1) / 2147483646
}

/** Chullpa de Sillustani: torre funeraria de piedra, más ancha arriba que abajo */
function chullpa() {
  const group = new THREE.Group()
  const r = rand(42)
  const courses = 22
  const height = 3.2
  for (let i = 0; i < courses; i++) {
    const t = i / (courses - 1)
    const radius = 0.62 + t * 0.26
    const geo = new THREE.CylinderGeometry(radius, radius - 0.012, height / courses, 18, 1)
    const shade = 0.42 + r() * 0.12
    const ring = new THREE.Mesh(geo, mat(new THREE.Color(shade + 0.08, shade + 0.03, shade - 0.04)))
    ring.position.y = t * height
    ring.rotation.y = r() * Math.PI
    group.add(ring)
  }
  // Cornisa y cúpula
  const cornice = new THREE.Mesh(new THREE.CylinderGeometry(0.96, 0.9, 0.12, 18), mat('#6e6457'))
  cornice.position.y = height + 0.04
  group.add(cornice)
  const dome = new THREE.Mesh(new THREE.SphereGeometry(0.92, 18, 8, 0, Math.PI * 2, 0, Math.PI / 2.4), mat('#776c5e'))
  dome.position.y = height + 0.08
  group.add(dome)
  // Pequeña entrada orientada al este
  const door = new THREE.Mesh(new THREE.BoxGeometry(0.22, 0.28, 0.12), mat('#2b241d'))
  door.position.set(0, 0.12, 0.66)
  group.add(door)
  // Plataforma de piedra y pasto de puna
  const base = new THREE.Mesh(new THREE.CylinderGeometry(1.5, 1.7, 0.2, 20), mat('#8a7a5c'))
  base.position.y = -0.2
  group.add(base)
  for (let i = 0; i < 40; i++) {
    const tuft = new THREE.Mesh(new THREE.ConeGeometry(0.05, 0.22, 4), mat('#b49a52'))
    const a = r() * Math.PI * 2
    const d = 1 + r() * 0.6
    tuft.position.set(Math.cos(a) * d, -0.02, Math.sin(a) * d)
    group.add(tuft)
  }
  group.name = 'Chullpa de Sillustani'
  return group
}

/** Balsa de totora de los Uros con proa de puma */
function balsa() {
  const group = new THREE.Group()
  const totora = mat('#d8bf72')
  const dark = mat('#b8994a')
  // Casco: dos haces de totora curvados
  for (const z of [-0.28, 0.28]) {
    const curve = new THREE.CatmullRomCurve3([
      new THREE.Vector3(-2.2, 0.55, z * 0.6),
      new THREE.Vector3(-1.6, 0.12, z),
      new THREE.Vector3(0, 0, z * 1.1),
      new THREE.Vector3(1.6, 0.12, z),
      new THREE.Vector3(2.1, 0.7, z * 0.5),
    ])
    group.add(new THREE.Mesh(new THREE.TubeGeometry(curve, 40, 0.28, 10), totora))
  }
  // Amarres
  for (let x = -1.6; x <= 1.6; x += 0.4) {
    const tie = new THREE.Mesh(new THREE.TorusGeometry(0.42, 0.025, 6, 16), mat('#7a5a2a'))
    tie.position.set(x, 0.1, 0)
    tie.rotation.y = Math.PI / 2
    group.add(tie)
  }
  // Cuello y cabeza de puma
  const neck = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.2, 0.9, 8), dark)
  neck.position.set(2.25, 1.05, 0)
  neck.rotation.z = -0.35
  group.add(neck)
  const head = new THREE.Mesh(new THREE.SphereGeometry(0.24, 12, 10), dark)
  head.position.set(2.42, 1.52, 0)
  head.scale.set(1.3, 1, 1)
  group.add(head)
  for (const z of [-0.12, 0.12]) {
    const ear = new THREE.Mesh(new THREE.ConeGeometry(0.07, 0.16, 5), dark)
    ear.position.set(2.36, 1.74, z)
    group.add(ear)
  }
  // Popa en espiral
  const tail = new THREE.Mesh(new THREE.TorusGeometry(0.22, 0.09, 8, 16, Math.PI * 1.4), dark)
  tail.position.set(-2.3, 0.8, 0)
  group.add(tail)
  // Toldo textil
  const poles = [[-0.9, -0.45], [-0.9, 0.45], [0.5, -0.45], [0.5, 0.45]]
  for (const [x, z] of poles) {
    const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.9, 6), mat('#6b4f2a'))
    pole.position.set(x, 0.6, z)
    group.add(pole)
  }
  const awning = new THREE.Mesh(new THREE.BoxGeometry(1.6, 0.04, 1.05), mat('#c8452f', 0.8))
  awning.position.set(-0.2, 1.06, 0)
  group.add(awning)
  const stripe = new THREE.Mesh(new THREE.BoxGeometry(1.62, 0.045, 0.16), mat('#4f7fb8', 0.8))
  stripe.position.set(-0.2, 1.07, 0)
  group.add(stripe)
  group.name = 'Balsa de totora'
  return group
}

/** Casa de totora de las islas flotantes */
function casaUros() {
  const group = new THREE.Group()
  const island = new THREE.Mesh(new THREE.CylinderGeometry(2.2, 2.35, 0.35, 28), mat('#d8bf72'))
  island.position.y = -0.18
  group.add(island)
  const walls = new THREE.Mesh(new THREE.BoxGeometry(1.4, 1.1, 1.9), mat('#c9a85a'))
  walls.position.y = 0.55
  group.add(walls)
  const roof = new THREE.Mesh(new THREE.CylinderGeometry(0.95, 0.95, 2.05, 3), mat('#b8994a'))
  roof.rotation.set(Math.PI / 2, 0, Math.PI / 2)
  roof.position.y = 1.55
  group.add(roof)
  const door = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.72, 0.05), mat('#4f7fb8', 0.7))
  door.position.set(0, 0.36, 0.96)
  group.add(door)
  // Fogón de barro y haces de totora apilados
  const stove = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.28, 0.3, 10), mat('#8c5a3c'))
  stove.position.set(1.3, 0.15, 0.9)
  group.add(stove)
  for (let i = 0; i < 5; i++) {
    const bundle = new THREE.Mesh(new THREE.CylinderGeometry(0.1, 0.1, 1.2, 8), mat('#e2cc85'))
    bundle.rotation.z = Math.PI / 2
    bundle.position.set(-1.25, 0.1 + (i % 2) * 0.18, -0.6 + i * 0.22)
    group.add(bundle)
  }
  group.name = 'Casa de totora'
  return group
}

function exportGlb(object, file) {
  const scene = new THREE.Scene()
  scene.add(object)
  return new Promise((resolve, reject) => {
    new GLTFExporter().parse(
      scene,
      (result) => {
        writeFileSync(join(outDir, file), Buffer.from(result))
        resolve(file)
      },
      reject,
      { binary: true },
    )
  })
}

const models = [
  { id: 'chullpa', file: 'chullpa-sillustani.glb', object: chullpa(), title: 'Chullpa de Sillustani', description: 'Torre funeraria preinca de la laguna Umayo, más ancha en la parte alta.' },
  { id: 'balsa', file: 'balsa-totora.glb', object: balsa(), title: 'Balsa de totora', description: 'Embarcación de los Uros con proa en forma de cabeza de puma.' },
  { id: 'casa-uros', file: 'casa-uros.glb', object: casaUros(), title: 'Casa en isla flotante', description: 'Vivienda de totora sobre una isla de los Uros, con fogón y haces de juncos.' },
]

for (const m of models) {
  await exportGlb(m.object, m.file)
  console.log('✓', m.file)
}

writeFileSync(
  join(outDir, 'manifest.json'),
  JSON.stringify(
    models.map(({ id, file, title, description }) => ({ id, file, title, description, credit: 'Modelo ilustrativo generado por Ñan Puno' })),
    null,
    2,
  ) + '\n',
)
console.log('✓ manifest.json')
