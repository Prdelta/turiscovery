import { Html, OrbitControls, Sky } from '@react-three/drei'
import { Canvas, useFrame } from '@react-three/fiber'
import { useMemo, useRef, type ReactNode } from 'react'
import * as THREE from 'three'
import type { Hotspot } from './support'

/*
  Lago Titicaca generado por código:
  agua animada con shader propio, cordillera con nieve en el horizonte,
  islas flotantes de totora de los Uros con casas y balsas, y la isla de Taquile.
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
      const dist = Math.hypot(x, z)
      const r = dist + noise2(x * 0.6, z * 0.6) * 14
      let h = -4
      if (r >= LAKE_RADIUS - 4) {
        // Lomas suaves del altiplano junto a la orilla
        const shoreRamp = THREE.MathUtils.smoothstep(r, LAKE_RADIUS - 4, LAKE_RADIUS + 60)
        const hills = shoreRamp * (4 + (noise2(x * 1.3, z * 1.3) + 1) * 6)
        // Cordillera Real lejana, sobre todo hacia el norte y el este
        const far = THREE.MathUtils.smoothstep(dist, 260, 420)
        const north = 0.3 + Math.max(0, -z / Math.max(dist, 1)) * 0.9
        const ridge = Math.max(0, noise2(x * 1.1, z * 1.1) + 0.35) + Math.max(0, noise2(x * 2.6 + 40, z * 2.6)) * 0.35
        const peaks = far * north * ridge * 42
        h = hills + peaks
      }
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

function Taquile() {
  const geometry = useMemo(() => {
    const geo = new THREE.SphereGeometry(16, 48, 24, 0, Math.PI * 2, 0, Math.PI / 2)
    const pos = geo.attributes.position as THREE.BufferAttribute
    for (let i = 0; i < pos.count; i++) {
      const x = pos.getX(i)
      const z = pos.getZ(i)
      const y = pos.getY(i)
      // Terrazas de cultivo: alturas escalonadas
      const terraced = Math.round(y * 0.42 * (1 + noise2(x * 3, z * 3) * 0.2) * 2.2) / 2.2
      pos.setY(i, terraced)
    }
    geo.computeVertexNormals()
    return geo
  }, [])

  return (
    <mesh geometry={geometry} position={[62, -0.6, -48]} scale={[1.6, 1, 0.8]} castShadow receiveShadow>
      <meshStandardMaterial color="#7a8a4a" roughness={1} flatShading />
    </mesh>
  )
}

/* ---------- Escena ---------- */

const SUN = new THREE.Vector3(80, 38, -140)
const FOG = '#bcd3ea'

function Islands({ animate }: { animate: boolean }) {
  return (
    <>
      <UrosIsland position={[-6, 0, 6]} radius={6} seed={3} animate={animate} />
      <UrosIsland position={[12, 0, -4]} radius={4.5} seed={7} animate={animate} />
      <UrosIsland position={[-20, 0, -10]} radius={3.6} seed={11} animate={animate} />
      <UrosIsland position={[4, 0, 22]} radius={3.2} seed={17} animate={animate} />
      <ReedBoat position={[22, 0.15, 14]} rotation={2.2} bob={1.4} animate={animate} />
      <ReedBoat position={[-14, 0.15, 24]} rotation={-0.6} bob={2.7} animate={animate} />
      <Taquile />
    </>
  )
}

function AutoOrbit({ enabled }: { enabled: boolean }) {
  useFrame(({ camera, clock }) => {
    if (!enabled) return
    const t = clock.elapsedTime * 0.04
    camera.position.set(Math.sin(t) * 62, 17 + Math.sin(t * 2) * 2, Math.cos(t) * 62)
    camera.lookAt(0, 2, 0)
  })
  return null
}

function prefersReducedMotion() {
  return typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

interface LakeSceneProps {
  /** Exploración libre con el ratón (página Puno 3D); si es false, la cámara orbita sola */
  interactive?: boolean
  hotspots?: Hotspot[]
  onHotspot?: (h: Hotspot) => void
  activeHotspot?: string | null
  children?: ReactNode
}

export default function LakeScene({ interactive = false, hotspots, onHotspot, activeHotspot, children }: LakeSceneProps) {
  const animate = !prefersReducedMotion()

  return (
    <Canvas
      shadows
      dpr={[1, 1.75]}
      camera={{ position: [0, 17, 62], fov: 45, near: 0.5, far: 1200 }}
      gl={{ antialias: true, powerPreference: 'high-performance' }}
      frameloop={animate || interactive ? 'always' : 'demand'}
    >
      <fog attach="fog" args={[FOG, 220, 760]} />
      <Sky distance={1000} sunPosition={SUN.toArray()} turbidity={2.5} rayleigh={0.6} mieCoefficient={0.004} mieDirectionalG={0.85} />
      <hemisphereLight args={['#dfeeff', '#6b5a3c', 0.9]} />
      <directionalLight
        position={SUN.toArray()}
        intensity={2.2}
        castShadow
        shadow-mapSize={[1024, 1024]}
        shadow-camera-left={-40}
        shadow-camera-right={40}
        shadow-camera-top={40}
        shadow-camera-bottom={-40}
      />
      <Water sunDir={SUN} fogColor={FOG} animate={animate} />
      <Terrain />
      <Islands animate={animate} />

      {hotspots?.map((h) => (
        <Html key={h.label} position={h.position} center zIndexRange={[20, 0]}>
          <button
            onClick={() => onHotspot?.(h)}
            className={`whitespace-nowrap rounded-full px-3 py-1.5 text-sm font-semibold shadow-md transition-colors ${
              activeHotspot === h.label ? 'bg-mint text-ink' : 'bg-white text-ink hover:bg-mint'
            }`}
          >
            {h.label}
          </button>
        </Html>
      ))}

      {interactive ? (
        <OrbitControls
          target={[0, 2, 0]}
          enableDamping
          maxPolarAngle={Math.PI / 2.15}
          minDistance={12}
          maxDistance={260}
          autoRotate={animate && !activeHotspot}
          autoRotateSpeed={0.35}
        />
      ) : (
        <AutoOrbit enabled={animate} />
      )}
      {children}
    </Canvas>
  )
}
