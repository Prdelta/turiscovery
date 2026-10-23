import { Bounds, Center, ContactShadows, Environment, Html, Lightformer, OrbitControls, useGLTF, useProgress } from '@react-three/drei'
import { Canvas } from '@react-three/fiber'
import { Component, Suspense, type ReactNode } from 'react'

/*
  Visor de modelos .glb / .gltf.
  Centra y encuadra cualquier modelo automáticamente, así que basta con
  copiar el archivo a /public/models y registrarlo en models/manifest.json.
*/

function Loader() {
  const { progress } = useProgress()
  return (
    <Html center>
      <div className="rounded-full bg-white px-4 py-2 text-sm font-semibold text-ink shadow">Cargando modelo… {Math.round(progress)}%</div>
    </Html>
  )
}

function Model({ url }: { url: string }) {
  const { scene } = useGLTF(url)
  return (
    <Center>
      <primitive object={scene} />
    </Center>
  )
}

/** Se reinicia al cambiar de modelo porque se monta con key={url} */
class ModelErrorBoundary extends Component<{ children: ReactNode }, { failed: boolean }> {
  state = { failed: false }
  static getDerivedStateFromError() {
    return { failed: true }
  }
  render() {
    if (this.state.failed) {
      return (
        <Html center>
          <div className="w-64 rounded-2xl bg-white p-4 text-center text-sm text-ink shadow">
            No se pudo abrir este modelo. Comprueba que sea un archivo .glb o .gltf válido.
          </div>
        </Html>
      )
    }
    return this.props.children
  }
}

export default function ModelViewer({ url, autoRotate = true }: { url: string; autoRotate?: boolean }) {
  return (
    <Canvas shadows dpr={[1, 2]} camera={{ position: [3, 2, 5], fov: 40 }}>
      <color attach="background" args={['#eef2f8']} />
      <ambientLight intensity={0.5} />
      <directionalLight position={[5, 8, 4]} intensity={1.6} castShadow />
      {/* Iluminación de estudio local (sin descargar mapas HDR externos) */}
      <Environment resolution={256}>
        <Lightformer intensity={2} position={[0, 5, -5]} scale={[10, 5, 1]} color="#fff4e0" />
        <Lightformer intensity={1} position={[-5, 1, 2]} rotation-y={Math.PI / 2} scale={[8, 4, 1]} color="#b6dfe7" />
        <Lightformer intensity={0.8} position={[5, 1, 2]} rotation-y={-Math.PI / 2} scale={[8, 4, 1]} color="#ffffff" />
      </Environment>
      <ModelErrorBoundary key={url}>
        <Suspense fallback={<Loader />}>
          <Bounds fit clip observe margin={1.3} key={url}>
            <Model url={url} />
          </Bounds>
        </Suspense>
      </ModelErrorBoundary>
      <ContactShadows position={[0, -1.2, 0]} opacity={0.35} scale={12} blur={2.5} far={4} />
      <OrbitControls makeDefault autoRotate={autoRotate} autoRotateSpeed={0.8} enableDamping />
    </Canvas>
  )
}
