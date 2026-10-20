import { CloudDownload } from 'lucide-react'
import { useState } from 'react'
import { useAuth } from '@/shared/auth/AuthContext'
import { useOnline } from '@/shared/offline/offline'
import { Button } from '@/shared/ui/Button'
import { useToast } from '@/shared/ui/Toast'
import { saveForTrip } from './saveForTrip'

/** Descarga ahora los datos del viaje para usarlos sin señal */
export function SaveForTripButton({
  variant = 'secondary',
  size,
  fullWidth,
  label = 'Guardar para usar sin conexión',
}: {
  variant?: 'primary' | 'secondary' | 'mint'
  size?: 'sm' | 'md' | 'lg'
  fullWidth?: boolean
  label?: string
}) {
  const { user } = useAuth()
  const online = useOnline()
  const notify = useToast()
  const [loading, setLoading] = useState(false)

  return (
    <Button
      variant={variant}
      size={size}
      fullWidth={fullWidth}
      icon={<CloudDownload className="size-4.5" />}
      loading={loading}
      loadingText="Guardando…"
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
      {label}
    </Button>
  )
}
