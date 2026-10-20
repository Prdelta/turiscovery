import { OrbitControls, Sky } from '@react-three/drei'
import { Canvas, useFrame, useThree } from '@react-three/fiber'
import { useEffect, useMemo, useRef, type ReactNode } from 'react'
import * as THREE from 'three'
import { DYES } from '@/shared/textile/Weave'
import { PLACES, type Place } from '@/shared/config/places'

/*
  Lago Titicaca generado por código:
  agua animada con shader propio, cordillera con nieve en el horizonte,
  islas de totora de los Uros, Taquile, Amantaní, Sillustani, Chucuito y la ciudad de Puno
  en una geografía estilizada. Es la interfaz de la portada: se navega tocando cada lugar.
  No usa archivos externos, así que carga rápido y funciona sin conexión a un CDN de modelos.
*/

const LAKE_RADIUS = 120

/** Ruido determinista barato (suma de senos) para relieve y variaciones */
function noise2(x: number, z: number) {
  return (
    Math.sin(x * 0.045 + Math.cos(z * 0.03) * 2.1) * 0.5 +
    Math.sin(z * 0.06 + x * 0.02) * 0.3 +
    Math.sin((x + z) * 0.11) * 0.15 +
    Math.cos(x * 0.19 - z * 0.13) * 0.08
  )
}

function seeded(seed: number) {
  let s = seed
  return () => {
    s = (s * 16807) % 2147483647
    return (s - 1) / 2147483646
  }
}

/* ---------- Agua ---------- */

const waterVertex = /* glsl */ `
  uniform float uTime;
  varying vec3 vWorld;
  varying float vWave;
  void main() {
    vec3 p = position;
    float w = sin(p.x * 0.18 + uTime * 0.9) * 0.18
            + sin(p.y * 0.23 + uTime * 0.7) * 0.14
            + sin((p.x + p.y) * 0.5 + uTime * 1.6) * 0.05;
    p.z += w;
    vWave = w;
    vec4 world = modelMatrix * vec4(p, 1.0);
    vWorld = world.xyz;
    gl_Position = projectionMatrix * viewMatrix * world;
  }
`

const waterFragment = /* glsl */ `
  uniform vec3 uDeep;
  uniform vec3 uShallow;
  uniform vec3 uSky;
  uniform vec3 uSunDir;
  uniform vec3 uFogColor;
  uniform float uFogNear;
  uniform float uFogFar;
  varying vec3 vWorld;
  varying float vWave;
  void main() {
    vec3 n = normalize(cross(dFdx(vWorld), dFdy(vWorld)));
    if (n.y < 0.0) n = -n;
    vec3 viewDir = normalize(cameraPosition - vWorld);
    float fresnel = pow(1.0 - max(dot(n, viewDir), 0.0), 3.0);
    vec3 base = mix(uDeep, uShallow, clamp(vWave * 1.8 + 0.35, 0.0, 1.0));
    vec3 color = mix(base, uSky, fresnel * 0.75);
    vec3 halfV = normalize(uSunDir + viewDir);
    float spec = pow(max(dot(n, halfV), 0.0), 180.0);
    color += vec3(1.0, 0.95, 0.8) * spec * 1.6;
    float dist = length(cameraPosition - vWorld);
    float fog = smoothstep(uFogNear, uFogFar, dist);
    gl_FragColor = vec4(mix(color, uFogColor, fog), 1.0);
  }
`

function Water({ sunDir, fogColor, animate }: { sunDir: THREE.Vector3; fogColor: string; animate: boolean }) {
  const material = useRef<THREE.ShaderMaterial>(null)
  const uniforms = useMemo(
    () => ({
      uTime: { value: 0 },
      uDeep: { value: new THREE.Color('#0d2f66') },
      uShallow: { value: new THREE.Color('#1f5fa3') },
      uSky: { value: new THREE.Color('#9cc7ea') },
      uSunDir: { value: sunDir.clone().normalize() },
      uFogColor: { value: new THREE.Color(fogColor) },
      uFogNear: { value: 160 },
      uFogFar: { value: 520 },
    }),
    [sunDir, fogColor],
  )

  useFrame((_, delta) => {
    if (animate && material.current) material.current.uniforms.uTime.value += delta
  })

  return (
    <mesh rotation-x={-Math.PI / 2} position-y={0}>
      <planeGeometry args={[900, 900, 240, 240]} />
      <shaderMaterial ref={material} vertexShader={waterVertex} fragmentShader={waterFragment} uniforms={uniforms} />
    </mesh>
  )
}

/* ---------- Cordillera y orillas ---------- */

/** Altura del terreno: lago al centro, lomas en la orilla y la cordillera nevada a lo lejos */
export function terrainHeight(x: number, z: number) {
  const dist = Math.hypot(x, z)
  const r = dist + noise2(x * 0.6, z * 0.6) * 14
  if (r < LAKE_RADIUS - 4) return -4
  // Lomas suaves del altiplano junto a la orilla
  const shoreRamp = THREE.MathUtils.smoothstep(r, LAKE_RADIUS - 4, LAKE_RADIUS + 60)
  const hills = shoreRamp * (4 + (noise2(x * 1.3, z * 1.3) + 1) * 6)
  // Cordillera Real lejana, sobre todo hacia el norte y el este
  const far = THREE.MathUtils.smoothstep(dist, 260, 420)
  const north = 0.3 + Math.max(0, -z / Math.max(dist, 1)) * 0.9
  const ridge = Math.max(0, noise2(x * 1.1, z * 1.1) + 0.35) + Math.max(0, noise2(x * 2.6 + 40, z * 2.6)) * 0.35
  return hills + far * north * ridge * 42
}

function Terrain() {
  const geometry = useMemo(() => {
    const geo = new THREE.PlaneGeometry(1100, 1100, 300, 300)
    geo.rotateX(-Math.PI / 2)
    const pos = geo.attributes.position as THREE.BufferAttribute
    const colors = new Float32Array(pos.count * 3)
    const shore = new THREE.Color('#a8945f')
    const puna = new THREE.Color('#8f7d4f')
    const rock = new THREE.Color('#6d6152')
    const snow = new THREE.Color('#f4f6fa')
    const c = new THREE.Color()

    for (let i = 0; i < pos.count; i++) {
      const x = pos.getX(i)
      const z = pos.getZ(i)
      const h = terrainHeight(x, z)
      pos.setY(i, h)

      if (h > 46) c.copy(snow)
      else if (h > 28) c.copy(rock).lerp(snow, (h - 28) / 18)
      else if (h > 8) c.copy(puna).lerp(rock, (h - 8) / 20)
      else c.copy(shore).lerp(puna, Math.max(0, h) / 8)
      colors.set([c.r, c.g, c.b], i * 3)
    }
    geo.setAttribute('color', new THREE.BufferAttribute(colors, 3))
    geo.computeVertexNormals()
    return geo
  }, [])

  return (
    <mesh geometry={geometry} receiveShadow>
      <meshStandardMaterial vertexColors roughness={0.95} flatShading />
    </mesh>
  )
}

/* ---------- Islas de los Uros ---------- */

const TOTORA = '#d8bf72'
const TOTORA_DARK = '#b8994a'

function ReedHouse({ position, rotation = 0, color = TOTORA_DARK }: { position: [number, number, number]; rotation?: number; color?: string }) {
  return (
    <group position={position} rotation-y={rotation}>
      <mesh position-y={0.55} castShadow>
        <boxGeometry args={[1.3, 1.1, 1.8]} />
        <meshStandardMaterial color={color} roughness={1} />
      </mesh>
      {/* Techo a dos aguas */}
      <mesh position-y={1.45} rotation={[Math.PI / 2, 0, Math.PI / 2]} castShadow>
        <cylinderGeometry args={[0.85, 0.85, 1.95, 3]} />
        <meshStandardMaterial color={TOTORA} roughness={1} flatShading />
      </mesh>
    </group>
  )
}

/** Balsa de totora con proa en forma de cabeza de puma */
function ReedBoat({ position, rotation = 0, bob = 0, animate }: { position: [number, number, number]; rotation?: number; bob?: number; animate: boolean }) {
  const ref = useRef<THREE.Group>(null)
  useFrame(({ clock }) => {
    if (!animate || !ref.current) return
    const t = clock.elapsedTime + bob
    ref.current.position.y = position[1] + Math.sin(t * 1.3) * 0.08
    ref.current.rotation.z = Math.sin(t * 0.9) * 0.03
  })
  return (
    <group ref={ref} position={position} rotation-y={rotation}>
      <mesh rotation-z={Math.PI / 2} scale={[1, 1, 0.85]} castShadow>
        <capsuleGeometry args={[0.42, 3.2, 6, 12]} />
        <meshStandardMaterial color={TOTORA} roughness={1} />
      </mesh>
      {/* Cuello y cabeza del puma */}
      <mesh position={[2.15, 0.65, 0]} rotation-z={-0.55}>
        <cylinderGeometry args={[0.16, 0.26, 1.3, 8]} />
        <meshStandardMaterial color={TOTORA_DARK} roughness={1} />
      </mesh>
      <mesh position={[2.55, 1.25, 0]} castShadow>
        <sphereGeometry args={[0.3, 10, 10]} />
        <meshStandardMaterial color={TOTORA_DARK} roughness={1} />
      </mesh>
      {/* Toldo */}
      <mesh position={[-0.3, 0.75, 0]}>
        <boxGeometry args={[1.4, 0.06, 0.9]} />
        <meshStandardMaterial color="#c8452f" roughness={0.8} />
      </mesh>
    </group>
  )
}

function UrosIsland({ position, radius, seed, animate }: { position: [number, number, number]; radius: number; seed: number; animate: boolean }) {
  const houses = useMemo(() => {
    const rnd = seeded(seed)
    const n = Math.max(2, Math.round(radius / 1.6))
    return Array.from({ length: n }, (_, i) => {
      const a = (i / n) * Math.PI * 2 + rnd() * 0.4
      const d = radius * (0.45 + rnd() * 0.25)
      const palette = [TOTORA_DARK, '#c9a85a', '#4f7fb8', '#c8452f']
      return {
        pos: [Math.cos(a) * d, 0.35, Math.sin(a) * d] as [number, number, number],
        rot: -a + Math.PI / 2,
        color: palette[Math.floor(rnd() * palette.length)],
      }
    })
  }, [radius, seed])

  return (
    <group position={position}>
      <mesh position-y={0.1} receiveShadow castShadow>
        <cylinderGeometry args={[radius, radius * 1.08, 0.55, 28]} />
        <meshStandardMaterial color={TOTORA} roughness={1} />
      </mesh>
      {houses.map((h, i) => (
        <ReedHouse key={i} position={h.pos} rotation={h.rot} color={h.color} />
      ))}
      {/* Mirador */}
      <mesh position={[0, 2, 0]} castShadow>
        <cylinderGeometry args={[0.25, 0.5, 4, 8]} />
        <meshStandardMaterial color={TOTORA_DARK} roughness={1} />
      </mesh>
      <ReedBoat position={[radius + 2.2, 0.15, 0.5]} rotation={0.3 + seed} bob={seed} animate={animate} />
    </group>
  )
}

/* ---------- Lugares ---------- */

const SUN = new THREE.Vector3(80, 38, -140)
const FOG = '#bcd3ea'

function groundAt(x: number, z: number) {
  return Math.max(0, terrainHeight(x, z))
}

/** Casas de adobe con techo de calamina y una iglesia de dos torres */
function Town({ position, houses, seed, church = true }: { position: [number, number, number]; houses: number; seed: number; church?: boolean }) {
  const items = useMemo(() => {
    const rnd = seeded(seed)
    const walls = ['#e7dcc6', '#d9c3a0', '#c98f63', '#efe7da']
    return Array.from({ length: houses }, () => {
      const x = position[0] + (rnd() - 0.5) * 22
      const z = position[2] + (rnd() - 0.5) * 22
      const h = 1.2 + rnd() * 2.2
      return { x, z, y: groundAt(x, z), h, w: 1.6 + rnd() * 1.6, d: 1.6 + rnd() * 1.6, color: walls[Math.floor(rnd() * walls.length)], rot: rnd() * Math.PI }
    })
  }, [position, houses, seed])
  const cy = groundAt(position[0], position[2])

  return (
    <group>
      {items.map((b, i) => (
        <group key={i} position={[b.x, b.y, b.z]} rotation-y={b.rot}>
          <mesh position-y={b.h / 2} castShadow>
            <boxGeometry args={[b.w, b.h, b.d]} />
            <meshStandardMaterial color={b.color} roughness={0.9} />
          </mesh>
          <mesh position-y={b.h + 0.08}>
            <boxGeometry args={[b.w + 0.2, 0.16, b.d + 0.2]} />
            <meshStandardMaterial color="#9aa1a8" metalness={0.4} roughness={0.5} />
          </mesh>
        </group>
      ))}
      {church && (
        <group position={[position[0], cy, position[2]]}>
          <mesh position-y={3} castShadow>
            <boxGeometry args={[4, 6, 8]} />
            <meshStandardMaterial color="#c9b49a" roughness={0.9} />
          </mesh>
          {[-1.4, 1.4].map((x) => (
            <group key={x} position={[x, 0, 4.4]}>
              <mesh position-y={4.6} castShadow>
                <boxGeometry args={[1.6, 9.2, 1.6]} />
                <meshStandardMaterial color="#c2ab8e" roughness={0.9} />
              </mesh>
              <mesh position-y={9.9}>
                <coneGeometry args={[1, 1.6, 4]} />
                <meshStandardMaterial color="#8a6a4a" roughness={0.9} />
              </mesh>
            </group>
          ))}
        </group>
      )}
    </group>
  )
}

/** Isla con terrazas de cultivo (Taquile, Amantaní) */
function TerracedIsland({ position, radius, stretch = [1.6, 0.8], seed }: { position: [number, number, number]; radius: number; stretch?: [number, number]; seed: number }) {
  const geometry = useMemo(() => {
    const geo = new THREE.SphereGeometry(radius, 56, 28, 0, Math.PI * 2, 0, Math.PI / 2)
    const pos = geo.attributes.position as THREE.BufferAttribute
    for (let i = 0; i < pos.count; i++) {
      const x = pos.getX(i)
      const z = pos.getZ(i)
      const y = pos.getY(i)
      const terraced = Math.round(y * 0.42 * (1 + noise2(x * 3 + seed, z * 3) * 0.2) * 2.2) / 2.2
      pos.setY(i, terraced)
    }
    geo.computeVertexNormals()
    return geo
  }, [radius, seed])

  return (
    <mesh geometry={geometry} position={[position[0], -0.6, position[2]]} scale={[stretch[0], 1, stretch[1]]} castShadow receiveShadow>
      <meshStandardMaterial color="#7a8a4a" roughness={1} flatShading />
    </mesh>
  )
}

const CHULLPAS: [number, number, number][] = [
  [0, 0, 7],
  [6, -3, 5.5],
  [-5, 3, 6],
  [3, 6, 4.5],
]

/** Chullpas de Sillustani junto a la laguna Umayo */
function Sillustani({ position }: { position: [number, number, number] }) {
  const lagoonY = groundAt(position[0] + 14, position[2] + 8) + 0.25
  return (
    <group>
      {CHULLPAS.map(([dx, dz, h], i) => {
        const x = position[0] + dx
        const z = position[2] + dz
        const y = groundAt(x, z)
        return (
          <group key={i} position={[x, y, z]}>
            {/* Más anchas arriba que abajo, como las chullpas reales */}
            <mesh position-y={h / 2} castShadow>
              <cylinderGeometry args={[1.5, 1.1, h, 14]} />
              <meshStandardMaterial color="#8d8172" roughness={1} flatShading />
            </mesh>
            <mesh position-y={h + 0.2}>
              <cylinderGeometry args={[1.62, 1.55, 0.4, 14]} />
              <meshStandardMaterial color="#776c5e" roughness={1} />
            </mesh>
          </group>
        )
      })}
      <mesh rotation-x={-Math.PI / 2} position={[position[0] + 14, lagoonY, position[2] + 8]}>
        <circleGeometry args={[9, 32]} />
        <meshStandardMaterial color="#2a5d93" roughness={0.3} metalness={0.1} />
      </mesh>
    </group>
  )
}

function scenePos(id: string) {
  return PLACES.find((p) => p.id === id)!.scene
}

function Places({ animate }: { animate: boolean }) {
  const [px, , pz] = scenePos('puno')
  const [ux, , uz] = scenePos('uros')
  const [tx, , tz] = scenePos('taquile')
  const [ax, , az] = scenePos('amantani')
  const [cx, , cz] = scenePos('chucuito')
  return (
    <>
      <Town position={[px, 0, pz]} houses={70} seed={5} />
      <UrosIsland position={[ux, 0, uz]} radius={6} seed={3} animate={animate} />
      <UrosIsland position={[ux + 14, 0, uz - 9]} radius={4.5} seed={7} animate={animate} />
      <UrosIsland position={[ux - 6, 0, uz - 15]} radius={3.6} seed={11} animate={animate} />
      <UrosIsland position={[ux + 8, 0, uz + 13]} radius={3.2} seed={17} animate={animate} />
      <ReedBoat position={[ux + 24, 0.15, uz + 4]} rotation={2.2} bob={1.4} animate={animate} />
      <ReedBoat position={[ux - 14, 0.15, uz + 12]} rotation={-0.6} bob={2.7} animate={animate} />
      <TerracedIsland position={[tx, 0, tz]} radius={16} seed={1} />
      <TerracedIsland position={[ax, 0, az]} radius={22} stretch={[1.3, 1]} seed={9} />
      <Sillustani position={scenePos('sillustani')} />
      <Town position={[cx, 0, cz]} houses={18} seed={13} />
    </>
  )
}

/* ---------- Cámara ---------- */

// Vista general desde el sureste, baja para ver el horizonte; los lugares quedan en la mitad superior
const OVERVIEW = { position: new THREE.Vector3(30, 62, 250), target: new THREE.Vector3(-45, 0, 20) }

function viewFor(place?: Place) {
  if (!place) return OVERVIEW
  const [x, , z] = place.scene
  const target = new THREE.Vector3(x, groundAt(x, z), z)
  return { target, position: target.clone().add(new THREE.Vector3(22, 34, 52)) }
}

/** Lleva la cámara suavemente al lugar elegido; después el usuario puede girar libremente */
function CameraRig({ focus, animate }: { focus?: Place; animate: boolean }) {
  const camera = useThree((s) => s.camera)
  const controls = useThree((s) => s.controls) as unknown as { target: THREE.Vector3; update: () => void } | null
  const goal = useRef<ReturnType<typeof viewFor> | null>(null)

  useEffect(() => {
    goal.current = viewFor(focus)
    if (!animate && controls) {
      camera.position.copy(goal.current.position)
      controls.target.copy(goal.current.target)
      controls.update()
      goal.current = null
    }
  }, [focus, animate, camera, controls])

  useFrame((_, delta) => {
    const g = goal.current
    if (!g || !controls) return
    const k = 1 - Math.pow(0.02, delta)
    camera.position.lerp(g.position, k)
    controls.target.lerp(g.target, k)
    controls.update()
    if (camera.position.distanceTo(g.position) < 0.5 && controls.target.distanceTo(g.target) < 0.5) goal.current = null
  })
  return null
}

/** Proyecta la posición 3D de cada lugar a la pantalla y mueve su etiqueta */
function LabelProjector({ places, labels }: { places: Place[]; labels: React.RefObject<Record<string, HTMLButtonElement | null>> }) {
  const camera = useThree((s) => s.camera)
  const size = useThree((s) => s.size)
  const points = useMemo(
    () => places.map((p) => ({ id: p.id, v: new THREE.Vector3(p.scene[0], groundAt(p.scene[0], p.scene[2]) + 14, p.scene[2]) })),
    [places],
  )
  const tmp = useMemo(() => new THREE.Vector3(), [])

  useFrame(() => {
    for (const { id, v } of points) {
      const el = labels.current?.[id]
      if (!el) continue
      tmp.copy(v).project(camera)
      const visible = tmp.z < 1 && Math.abs(tmp.x) < 1.1 && Math.abs(tmp.y) < 1.1
      el.style.visibility = visible ? 'visible' : 'hidden'
      if (!visible) continue
      const x = ((tmp.x + 1) / 2) * size.width
      const y = ((1 - tmp.y) / 2) * size.height
      el.style.transform = `translate(-50%, -50%) translate(${x.toFixed(1)}px, ${y.toFixed(1)}px)`
    }
  })
  return null
}

function prefersReducedMotion() {
  return typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

/* ---------- Escena ---------- */

interface LakeSceneProps {
  /** Muestra las etiquetas de los lugares y permite elegir uno */
  places?: Place[]
  selected?: string | null
  onSelect?: (id: string | null) => void
  /** Solo para fondo: sin controles y girando despacio */
  decorative?: boolean
  children?: ReactNode
}

export default function LakeScene({ places, selected, onSelect, decorative = false, children }: LakeSceneProps) {
  const animate = !prefersReducedMotion()
  const focus = places?.find((p) => p.id === selected)
  const labels = useRef<Record<string, HTMLButtonElement | null>>({})

  return (
    <div className="relative size-full overflow-hidden">
    <Canvas
      shadows
      dpr={[1, 1.75]}
      camera={{ position: OVERVIEW.position.toArray(), fov: 42, near: 0.5, far: 1400 }}
      gl={{ antialias: true, powerPreference: 'high-performance' }}
    >
      <fog attach="fog" args={[FOG, 260, 820]} />
      <Sky distance={1000} sunPosition={SUN.toArray()} turbidity={2.5} rayleigh={0.6} mieCoefficient={0.004} mieDirectionalG={0.85} />
      <hemisphereLight args={['#dfeeff', '#6b5a3c', 0.9]} />
      <directionalLight
        position={SUN.toArray()}
        intensity={2.2}
        castShadow
        shadow-mapSize={[2048, 2048]}
        shadow-camera-left={-200}
        shadow-camera-right={200}
        shadow-camera-top={200}
        shadow-camera-bottom={-200}
      />
      <Water sunDir={SUN} fogColor={FOG} animate={animate} />
      <Terrain />
      <Places animate={animate} />

      {places && <LabelProjector places={places} labels={labels} />}

      <OrbitControls
        makeDefault
        enabled={!decorative}
        target={OVERVIEW.target.toArray()}
        enableDamping
        maxPolarAngle={Math.PI / 2.2}
        minDistance={20}
        maxDistance={420}
        autoRotate={animate && decorative}
        autoRotateSpeed={decorative ? 0.25 : 0.12}
      />
      {!decorative && <CameraRig focus={focus} animate={animate} />}
      {children}
    </Canvas>
      {/* Etiquetas de los lugares: botones normales del DOM, ubicados en cada cuadro por LabelProjector */}
      {places?.map((p) => {
        const active = selected === p.id
        return (
          <button
            key={p.id}
            ref={(el) => {
              labels.current[p.id] = el
            }}
            onClick={() => onSelect?.(active ? null : p.id)}
            aria-pressed={active}
            style={{ position: 'absolute', left: 0, top: 0, visibility: 'hidden' }}
            className={
              'flex items-center gap-1.5 whitespace-nowrap rounded-full py-1 pl-1 pr-3 text-sm font-semibold shadow-lg transition-colors ' +
              (active ? 'bg-mint text-ink' : 'bg-white/95 text-ink hover:bg-mint')
            }
          >
            <span className="inline-block size-5 rounded-full" style={{ background: DYES[p.dye] }} aria-hidden />
            {p.name}
          </button>
        )
      })}
    </div>
  )
}
