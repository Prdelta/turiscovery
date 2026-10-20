import { Download, WifiOff } from 'lucide-react'
import { useInstallPrompt, useOnline } from '@/shared/offline/offline'
import { Button } from '@/shared/ui/Button'

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

export function InstallAppButton({ className }: { className?: string }) {
  const install = useInstallPrompt()
  if (!install) return null
  return (
    <Button variant="mint" size="sm" className={className} icon={<Download className="size-4" />} onClick={install}>
      Instalar la app
    </Button>
  )
}
