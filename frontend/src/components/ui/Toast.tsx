import { CheckCircle2, WifiOff, X, XCircle } from 'lucide-react'
import { createContext, useCallback, useContext, useState, type ReactNode } from 'react'

/*
  Toasts de la guía Telar: fondo de tinta, texto blanco y, si corresponde,
  una acción en menta ("Deshacer"). La variante sin conexión avisa que algo
  se enviará cuando vuelva la señal.
*/

type ToastTone = 'success' | 'error' | 'offline'

interface ToastAction {
  label: string
  onClick: () => void
}

interface Toast {
  id: number
  tone: ToastTone
  message: string
  action?: ToastAction
}

type Notify = (message: string, tone?: ToastTone, options?: { action?: ToastAction }) => void

const ToastContext = createContext<Notify>(() => {})

const ICON: Record<ToastTone, ReactNode> = {
  success: <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-mint" aria-hidden />,
  error: <XCircle className="mt-0.5 size-5 shrink-0 text-[#ff8a8a]" aria-hidden />,
  offline: <WifiOff className="mt-0.5 size-5 shrink-0 text-sun" aria-hidden />,
}

export function ToastProvider({ children }: { children: ReactNode }) {
  const [toasts, setToasts] = useState<Toast[]>([])

  const dismiss = useCallback((id: number) => setToasts((t) => t.filter((x) => x.id !== id)), [])

  const notify = useCallback<Notify>(
    (message, tone = 'success', options) => {
      const id = Date.now() + Math.random()
      setToasts((t) => [...t, { id, tone, message, action: options?.action }])
      // Con acción se da más tiempo para usarla
      setTimeout(() => dismiss(id), options?.action ? 8000 : 4500)
    },
    [dismiss],
  )

  return (
    <ToastContext.Provider value={notify}>
      {children}
      <div aria-live="polite" className="pointer-events-none fixed inset-x-4 bottom-20 z-[2000] flex flex-col items-center gap-2 lg:inset-x-auto lg:bottom-6 lg:right-6 lg:items-end">
        {toasts.map((t) => (
          <div
            key={t.id}
            role={t.tone === 'error' ? 'alert' : 'status'}
            className="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-[var(--radius-control)] border-2 border-ink bg-ink px-4 py-3 text-sm text-white"
          >
            {ICON[t.tone]}
            <p className="flex-1">{t.message}</p>
            {t.action && (
              <button
                onClick={() => {
                  t.action!.onClick()
                  dismiss(t.id)
                }}
                className="font-bold text-mint underline decoration-2 underline-offset-4"
              >
                {t.action.label}
              </button>
            )}
            <button onClick={() => dismiss(t.id)} aria-label="Cerrar aviso" className="-m-1 inline-flex size-8 items-center justify-center opacity-70 hover:opacity-100">
              <X className="size-4" />
            </button>
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  )
}

// eslint-disable-next-line react-refresh/only-export-components
export function useToast() {
  return useContext(ToastContext)
}
