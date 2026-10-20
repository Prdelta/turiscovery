import { QueryClient } from '@tanstack/react-query'

/** Cliente de datos de toda la app: caché de 30 s y sin reintentos en errores del cliente */
export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      refetchOnWindowFocus: false,
      retry: (count, error) => {
        const status = (error as { response?: { status?: number } }).response?.status
        // No reintentar errores del cliente (404, 403, 422…)
        if (status && status >= 400 && status < 500) return false
        return count < 2
      },
    },
  },
})
