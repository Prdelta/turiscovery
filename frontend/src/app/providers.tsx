import { QueryClientProvider } from '@tanstack/react-query'
import type { ReactNode } from 'react'
import { queryClient } from '@/app/queryClient'
import { AuthProvider } from '@/shared/auth/AuthContext'
import { ToastProvider } from '@/shared/ui/Toast'

/** Proveedores globales (datos, sesión y avisos), en el orden en que dependen entre sí */
export function AppProviders({ children }: { children: ReactNode }) {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <ToastProvider>{children}</ToastProvider>
      </AuthProvider>
    </QueryClientProvider>
  )
}
