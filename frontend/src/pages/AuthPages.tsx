import { useState, type FormEvent, type ReactNode } from 'react'
import { Link, Navigate, useNavigate, useSearchParams } from 'react-router'
import { homeForRole, useAuth } from '../auth/AuthContext'
import { Button } from '../components/ui/Button'
import { Alert } from '../components/ui/Feedback'
import { Checkbox, TextField } from '../components/ui/Field'
import { parseApiError } from '../lib/api'
import { useDocumentTitle } from '../lib/hooks'

const API_URL = import.meta.env.VITE_API_URL ?? ''

/** Solo se permite volver a rutas internas tras iniciar sesión */
function safeNext(next: string | null) {
  return next && next.startsWith('/') && !next.startsWith('//') ? next : null
}

function AuthLayout({ title, text, children }: { title: string; text: string; children: ReactNode }) {
  return (
    <div className="grid min-h-[calc(100dvh-4rem)] lg:grid-cols-2">
      <div className="relative hidden bg-lake lg:block">
        <img src="/images/login_bg.png" alt="" className="absolute inset-0 size-full object-cover" />
        <div className="absolute inset-x-0 bottom-0 bg-lake-deep/80 p-10 text-white">
          <p className="font-display text-3xl font-extrabold text-white">Guarda lo que te gusta, reserva y obtén cupones.</p>
        </div>
      </div>
      <div className="flex items-center justify-center px-4 py-12">
        <div className="w-full max-w-md">
          <h1 className="text-4xl font-extrabold">{title}</h1>
          <p className="mt-2 text-ink-soft">{text}</p>
          <div className="mt-8">{children}</div>
        </div>
      </div>
    </div>
  )
}

function GoogleButton() {
  return (
    <a
      href={`${API_URL}/api/auth/google`}
      className="flex h-11 w-full items-center justify-center gap-3 rounded-full border border-line bg-white font-semibold hover:border-ink/40"
    >
      <svg viewBox="0 0 24 24" className="size-5" aria-hidden>
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1z" />
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z" />
        <path fill="#FBBC05" d="M5.84 14.1A6.6 6.6 0 0 1 5.5 12c0-.73.13-1.44.34-2.1V7.06H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.94l3.66-2.84z" />
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15A10.55 10.55 0 0 0 12 1 11 11 0 0 0 2.18 7.06l3.66 2.84C6.71 7.3 9.14 5.38 12 5.38z" />
      </svg>
      Continuar con Google
    </a>
  )
}

function Divider() {
  return (
    <div className="my-6 flex items-center gap-3 text-sm text-ink-soft">
      <span className="h-px flex-1 bg-line" /> o con tu correo <span className="h-px flex-1 bg-line" />
    </div>
  )
}

export function LoginPage() {
  useDocumentTitle('Iniciar sesión')
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [params] = useSearchParams()
  const next = safeNext(params.get('next'))
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [remember, setRemember] = useState(false)
  const [error, setError] = useState<string | null>(params.get('error') === 'google' ? 'No se pudo completar el acceso con Google. Inténtalo otra vez.' : null)
  const [loading, setLoading] = useState(false)

  if (user) return <Navigate to={next ?? homeForRole(user.role)} replace />

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    setLoading(true)
    try {
      const u = await login(email, password, remember)
      navigate(next ?? (u.role === 'tourist' ? '/' : homeForRole(u.role)), { replace: true })
    } catch (err) {
      setError(parseApiError(err).message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout title="Inicia sesión" text="Accede a tus favoritos, reservas y cupones.">
      <GoogleButton />
      <Divider />
      <form onSubmit={onSubmit} className="flex flex-col gap-4">
        <TextField label="Correo electrónico" type="email" autoComplete="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
        <TextField label="Contraseña" type="password" autoComplete="current-password" value={password} onChange={(e) => setPassword(e.target.value)} required />
        <Checkbox label="Mantener la sesión iniciada" checked={remember} onChange={(e) => setRemember(e.target.checked)} />
        {error && <Alert tone="error">{error}</Alert>}
        <Button type="submit" loading={loading}>Iniciar sesión</Button>
      </form>
      <p className="mt-6 text-center text-ink-soft">
        ¿No tienes cuenta? <Link to={`/registro${next ? `?next=${encodeURIComponent(next)}` : ''}`} className="font-semibold text-primary">Crea una gratis</Link>
      </p>
      <p className="mt-2 text-center text-sm text-ink-soft">¿Tienes un negocio en Puno? Escríbenos desde <Link to="/contacto" className="font-semibold text-primary">contacto</Link> para ser socio.</p>
    </AuthLayout>
  )
}

export function RegisterPage() {
  useDocumentTitle('Crear cuenta')
  const { user, register } = useAuth()
  const navigate = useNavigate()
  const [params] = useSearchParams()
  const next = safeNext(params.get('next'))
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  if (user) return <Navigate to={next ?? homeForRole(user.role)} replace />

  const set = (key: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement>) => setForm((f) => ({ ...f, [key]: e.target.value }))

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    setErrors({})
    if (form.password !== form.password_confirmation) {
      setErrors({ password_confirmation: 'Las contraseñas no coinciden.' })
      return
    }
    setLoading(true)
    try {
      await register(form)
      navigate(next ?? '/', { replace: true })
    } catch (err) {
      const parsed = parseApiError(err)
      setErrors(parsed.fields)
      if (!Object.keys(parsed.fields).length) setError(parsed.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout title="Crea tu cuenta" text="Gratis. Solo necesitas un correo.">
      <GoogleButton />
      <Divider />
      <form onSubmit={onSubmit} className="flex flex-col gap-4">
        <TextField label="Nombre" autoComplete="name" value={form.name} onChange={set('name')} error={errors.name} required />
        <TextField label="Correo electrónico" type="email" autoComplete="email" value={form.email} onChange={set('email')} error={errors.email} required />
        <TextField
          label="Contraseña"
          type="password"
          autoComplete="new-password"
          value={form.password}
          onChange={set('password')}
          error={errors.password}
          hint="Mínimo 8 caracteres con mayúsculas, minúsculas, un número y un símbolo."
          required
        />
        <TextField label="Repite la contraseña" type="password" autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} error={errors.password_confirmation} required />
        {error && <Alert tone="error">{error}</Alert>}
        <Button type="submit" loading={loading}>Crear cuenta</Button>
        <p className="text-xs text-ink-soft">
          Al crear tu cuenta aceptas los <Link to="/terminos" className="underline">términos</Link> y la <Link to="/privacidad" className="underline">política de privacidad</Link>.
        </p>
      </form>
      <p className="mt-6 text-center text-ink-soft">
        ¿Ya tienes cuenta? <Link to="/login" className="font-semibold text-primary">Inicia sesión</Link>
      </p>
    </AuthLayout>
  )
}
