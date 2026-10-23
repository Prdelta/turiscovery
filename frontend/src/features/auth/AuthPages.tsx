import { useState, type FormEvent, type ReactNode } from 'react'
import { Link, Navigate, useNavigate, useSearchParams } from 'react-router'
import { homeForRole, useAuth } from '@/shared/auth/AuthContext'
import { Brand } from '@/shared/ui/Identity'
import { MotifStrip, StitchRow, WovenBand } from '@/shared/textile/Weave'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { Alert } from '@/shared/ui/Feedback'
import { Checkbox, TextField } from '@/shared/ui/Field'
import { api, parseApiError } from '@/shared/api/client'
import { useToast } from '@/shared/ui/Toast'
import { useDocumentTitle } from '@/shared/lib/hooks'

const API_URL = import.meta.env.VITE_API_URL ?? ''

/** Errores con los que el backend devuelve al login desde el flujo de Google */
const GOOGLE_ERRORS: Record<string, string> = {
  google: 'No se pudo completar el acceso con Google. Inténtalo otra vez.',
  google_config: 'El acceso con Google todavía no está disponible. Entra con tu correo y contraseña.',
}

/** Solo se permite volver a rutas internas tras iniciar sesión */
function safeNext(next: string | null) {
  return next && next.startsWith('/') && !next.startsWith('//') ? next : null
}

function AuthLayout({ title, text, children }: { title: string; text: string; children: ReactNode }) {
  return (
    <div className="grid min-h-[calc(100dvh-4rem)] lg:grid-cols-2">
      {/* Foto con faja y texto sobre tinta sólida (sin degradados ni transparencias) */}
      <div className="hidden flex-col border-r-2 border-ink bg-ink lg:flex">
        <div className="relative flex-1">
          <img src="/images/login_bg.webp" alt="" className="absolute inset-0 size-full object-cover" />
        </div>
        <WovenBand motif="chakana" dye="cochinilla" height={20} />
        <div className="flex flex-col gap-4 p-10">
          <p className="t-h2 text-white">Guarda lo que te gusta, reserva y obtén cupones.</p>
          <MotifStrip motifs={['olas', 'chakana', 'ojo', 'llave', 'persona', 'estrella']} size={24} />
        </div>
      </div>
      <div className="flex items-center justify-center px-4 py-10">
        <div className="card w-full max-w-md p-6 sm:p-8">
          <Brand />
          <h1 className="t-h1 mt-6">{title}</h1>
          <p className="mt-2 text-ink-soft">{text}</p>
          <div className="mt-6">{children}</div>
        </div>
      </div>
    </div>
  )
}

function GoogleButton() {
  return (
    <a
      href={`${API_URL}/api/auth/google`}
      className="flex min-h-13 w-full items-center justify-center gap-3 rounded-[var(--radius-control)] border-2 border-ink bg-white font-bold hover:bg-sky"
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
    <div className="my-6 flex items-center gap-3 text-sm font-bold text-ink-soft">
      <StitchRow dye="anil" className="flex-1" /> o con tu correo <StitchRow dye="anil" className="flex-1" />
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
  const [error, setError] = useState<string | null>(GOOGLE_ERRORS[params.get('error') ?? ''] ?? null)
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
        <Link to={`/recuperar${email ? `?email=${encodeURIComponent(email)}` : ''}`} className="-mt-2 self-end text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">
          ¿Olvidaste tu contraseña?
        </Link>
        <Checkbox label="Mantener la sesión iniciada" checked={remember} onChange={(e) => setRemember(e.target.checked)} />
        {error && <Alert tone="error">{error}</Alert>}
        <Button type="submit" loading={loading} loadingText="Entrando…" fullWidth>Iniciar sesión</Button>
      </form>
      <p className="mt-6 text-center text-ink-soft">
        ¿No tienes cuenta? <Link to={`/registro${next ? `?next=${encodeURIComponent(next)}` : ''}`} className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Crea una gratis</Link>
      </p>
      <p className="mt-2 text-center text-sm text-ink-soft">¿Tienes un negocio en Puno? Escríbenos desde <Link to="/contacto" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">contacto</Link> para ser socio.</p>
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
        <Button type="submit" loading={loading} loadingText="Creando tu cuenta…" fullWidth>Crear cuenta</Button>
        <p className="text-xs text-ink-soft">
          Al crear tu cuenta aceptas los <Link to="/terminos" className="font-bold underline decoration-2 underline-offset-2">términos</Link> y la <Link to="/privacidad" className="font-bold underline decoration-2 underline-offset-2">política de privacidad</Link>.
        </p>
      </form>
      <p className="mt-6 text-center text-ink-soft">
        ¿Ya tienes cuenta? <Link to="/login" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Inicia sesión</Link>
      </p>
    </AuthLayout>
  )
}

export function ForgotPasswordPage() {
  useDocumentTitle('Recuperar contraseña')
  const [params] = useSearchParams()
  const [email, setEmail] = useState(params.get('email') ?? '')
  const [sent, setSent] = useState<string | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setError(null)
    setLoading(true)
    try {
      const res = await api.post<{ message: string }>('/forgot-password', { email })
      setSent(res.data.message)
    } catch (err) {
      setError(parseApiError(err).message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout title="Recupera tu contraseña" text="Te enviaremos un enlace a tu correo para crear una nueva.">
      {sent ? (
        <div className="flex flex-col gap-4">
          <Alert tone="success">{sent}</Alert>
          <p className="text-sm text-ink-soft">Revisa también la carpeta de spam. El enlace vence en una hora.</p>
          <Button type="button" variant="secondary" onClick={() => setSent(null)}>Usar otro correo</Button>
        </div>
      ) : (
        <form onSubmit={onSubmit} className="flex flex-col gap-4">
          <TextField label="Correo electrónico" type="email" autoComplete="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
          {error && <Alert tone="error">{error}</Alert>}
          <Button type="submit" loading={loading} loadingText="Enviando…" fullWidth>Enviar enlace</Button>
        </form>
      )}
      <p className="mt-6 text-center text-ink-soft">
        ¿La recordaste? <Link to="/login" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">Inicia sesión</Link>
      </p>
    </AuthLayout>
  )
}

export function ResetPasswordPage() {
  useDocumentTitle('Nueva contraseña')
  const navigate = useNavigate()
  const notify = useToast()
  const [params] = useSearchParams()
  const token = params.get('token') ?? ''
  const email = params.get('email') ?? ''
  const [form, setForm] = useState({ password: '', password_confirmation: '' })
  const [errors, setErrors] = useState<Record<string, string>>({})
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  if (!token || !email) {
    return (
      <AuthLayout title="Enlace incompleto" text="Abre el enlace tal como llegó a tu correo, o pide uno nuevo.">
        <ButtonLink to="/recuperar" fullWidth>Pedir un enlace nuevo</ButtonLink>
      </AuthLayout>
    )
  }

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
      const res = await api.post<{ message: string }>('/reset-password', { token, email, ...form })
      notify(res.data.message)
      navigate(`/login`, { replace: true })
    } catch (err) {
      const parsed = parseApiError(err)
      setErrors(parsed.fields)
      if (!parsed.fields.password) setError(parsed.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout title="Crea una nueva contraseña" text={`Para la cuenta ${email}.`}>
      <form onSubmit={onSubmit} className="flex flex-col gap-4">
        <TextField
          label="Nueva contraseña"
          type="password"
          autoComplete="new-password"
          value={form.password}
          onChange={set('password')}
          error={errors.password}
          hint="Mínimo 8 caracteres con mayúsculas, minúsculas, un número y un símbolo."
          required
        />
        <TextField label="Repite la contraseña" type="password" autoComplete="new-password" value={form.password_confirmation} onChange={set('password_confirmation')} error={errors.password_confirmation} required />
        {error && (
          <Alert tone="error">
            {error} <Link to="/recuperar" className="font-bold underline">Pedir otro enlace</Link>
          </Alert>
        )}
        <Button type="submit" loading={loading} loadingText="Guardando…" fullWidth>Guardar contraseña</Button>
      </form>
    </AuthLayout>
  )
}
