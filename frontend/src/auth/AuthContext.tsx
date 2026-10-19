import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from 'react'
import { useQueryClient } from '@tanstack/react-query'
import { api, ensureCsrf } from '../lib/api'
import type { Role, User } from '../lib/types'

interface RegisterInput {
  name: string
  email: string
  password: string
  password_confirmation: string
}

interface AuthState {
  user: User | null
  loading: boolean
  login: (email: string, password: string, remember?: boolean) => Promise<User>
  register: (input: RegisterInput) => Promise<User>
  logout: () => Promise<void>
  refresh: () => Promise<void>
  hasRole: (...roles: Role[]) => boolean
}

const AuthContext = createContext<AuthState | null>(null)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)
  const queryClient = useQueryClient()

  const refresh = useCallback(async () => {
    try {
      const res = await api.get<{ authenticated: boolean; user: User | null }>('/session/check')
      setUser(res.data.authenticated ? res.data.user : null)
    } catch {
      setUser(null)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    void refresh()
  }, [refresh])

  const login = useCallback(
    async (email: string, password: string, remember = false) => {
      await ensureCsrf(true)
      const res = await api.post<{ data: { user: User } }>('/session/login', { email, password, remember })
      // La sesión se regenera al iniciar sesión: se pide un token CSRF nuevo
      await ensureCsrf(true)
      setUser(res.data.data.user)
      queryClient.clear()
      return res.data.data.user
    },
    [queryClient],
  )

  const register = useCallback(
    async (input: RegisterInput) => {
      await ensureCsrf(true)
      const res = await api.post<{ data: { user: User } }>('/register', input)
      await ensureCsrf(true)
      setUser(res.data.data.user)
      queryClient.clear()
      return res.data.data.user
    },
    [queryClient],
  )

  const logout = useCallback(async () => {
    try {
      await api.post('/session/logout')
    } finally {
      setUser(null)
      queryClient.clear()
      await ensureCsrf(true)
    }
  }, [queryClient])

  const hasRole = useCallback((...roles: Role[]) => !!user && roles.includes(user.role), [user])

  const value = useMemo(
    () => ({ user, loading, login, register, logout, refresh, hasRole }),
    [user, loading, login, register, logout, refresh, hasRole],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth debe usarse dentro de <AuthProvider>')
  return ctx
}

/** Ruta de inicio según el rol del usuario */
// eslint-disable-next-line react-refresh/only-export-components
export function homeForRole(role: Role) {
  if (role === 'admin') return '/admin'
  if (role === 'socio') return '/dashboard'
  return '/cuenta'
}
