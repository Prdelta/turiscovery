import { CloudDownload, Download, WifiOff } from 'lucide-react'
import { useState } from 'react'
import { useAuth } from '../auth/AuthContext'
import { saveForTrip, useInstallPrompt, useOnline } from '../lib/offline'
import { Button } from './ui/Button'
import { useToast } from './ui/Toast'

/** Franja que avisa cuando no hay conexión (frecuente en el lago y las islas) */
export function OfflineBanner() {
  const online = useOnline()
  if (online) return null
  return (
    <div role="status" className="bg-sun text-ink">
      <p className="container-page flex items-center gap-2 py-2 text-sm font-semibold">
        <WifiOff className="size-4 shrink-0" aria-hidden />
        Sin conexión. Ves la información guardada la última vez; reservar o canjear necesita señal.
      </p>
    </div>
  )
}

/** Descarga ahora los datos del viaje para usarlos sin señal */
export function SaveForTripButton({ variant = 'secondary' }: { variant?: 'primary' | 'secondary' }) {
  const { user } = useAuth()
  const online = useOnline()
  const notify = useToast()
  const [loading, setLoading] = useState(false)

  return (
    <Button
      variant={variant}
      icon={<CloudDownload className="size-4.5" />}
      loading={loading}
      disabled={!online}
      onClick={async () => {
        setLoading(true)
        const { saved, failed } = await saveForTrip(!!user)
        setLoading(false)
        if (!('serviceWorker' in navigator) || !navigator.serviceWorker.controller) {
          notify('Abre la app instalada o recarga la página una vez para poder guardar los datos sin conexión.', 'error')
        } else if (failed && !saved) {
          notify('No se pudo guardar. Revisa tu conexión e inténtalo otra vez.', 'error')
        } else {
          notify(failed ? `Guardado (${failed} secciones no estaban disponibles)` : 'Listo: tus cupones, reservas y plan quedan disponibles sin conexión')
        }
      }}
    >
      Guardar para usar sin conexión
    </Button>
  )
}

export function InstallAppButton({ className }: { className?: string }) {
  const install = useInstallPrompt()
  if (!install) return null
  return (
    <Button variant="mint" size="sm" className={className} icon={<Download className="size-4" />} onClick={install}>
      Instalar la app
    </Button>
  )
}
