import { useMutation, useQuery } from '@tanstack/react-query'
import { Link, useSearchParams } from 'react-router'
import { api, parseApiError } from '@/shared/api/client'
import { useAuth } from '@/shared/auth/AuthContext'
import { SaveForTripButton } from '@/features/trip/SaveForTripButton'
import { PageHeader } from '@/shared/ui/PageHeader'
import { StitchRow, Tocapu } from '@/shared/textile/Weave'
import { StatTile, TEXT_LINK } from '@/shared/ui/Panel'
import { Button, ButtonLink } from '@/shared/ui/Button'
import { Alert, CardGridSkeleton } from '@/shared/ui/Feedback'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { accountQueries } from '@/features/account/api'
import { useContentList } from '@/features/catalog/api'
import { ContentCard } from '@/features/catalog/components/ContentCard'
import { CONTENT } from '@/shared/config/content'
import type { AnyContent, Interest, TravelPace, UserPreferences } from '@/shared/api/types'

const VERIFY_RESULT: Record<string, { tone: 'success' | 'error'; text: string }> = {
  '1': { tone: 'success', text: 'Listo, tu correo quedó verificado.' },
  error: { tone: 'error', text: 'Ese enlace no corresponde a tu cuenta. Pide uno nuevo.' },
  vencido: { tone: 'error', text: 'El enlace de verificación venció. Pide uno nuevo.' },
}

/** Resultado del enlace del correo (?verificado=) y aviso hasta confirmar el correo */
function EmailVerification({ verifiedAt }: { verifiedAt: string | null | undefined }) {
  const [params] = useSearchParams()
  const result = VERIFY_RESULT[params.get('verificado') ?? '']
  const resend = useMutation({
    mutationFn: async () => (await api.post<{ message: string }>('/email/verification-notification')).data.message,
  })

  const verified = !!verifiedAt
  if (verified && result?.tone !== 'success') return null

  return (
    <div className="mb-6 flex flex-col gap-3">
      {result && <Alert tone={result.tone}>{result.text}</Alert>}
      {!verified && (
        <Alert tone="info">
          <span className="block">Confirma tu correo para que los anfitriones puedan escribirte sobre tus reservas.</span>
          {resend.isSuccess ? (
            <span className="mt-2 block font-bold">{resend.data}</span>
          ) : (
            <span className="mt-2 block">
              <Button size="sm" variant="secondary" loading={resend.isPending} loadingText="Enviando…" onClick={() => resend.mutate()}>
                Enviar enlace de verificación
              </Button>
            </span>
          )}
          {resend.isError && <span className="mt-2 block text-danger">{parseApiError(resend.error).message}</span>}
        </Alert>
      )}
    </div>
  )
}

export function AccountHome() {
  useDocumentTitle('Mi cuenta')
  const { user } = useAuth()
  const { data } = useQuery(accountQueries.profile())
  const cards = [
    { to: '/cuenta/favoritos', label: 'Favoritos', value: data?.favorites_count, motif: 'estrella', dye: 'cochinilla' },
    { to: '/cuenta/reservas', label: 'Reservas', value: data?.bookings_count, motif: 'olas', dye: 'anil' },
    { to: '/cuenta/eventos', label: 'Asistencias', value: data?.event_attendances_count, motif: 'chakana', dye: 'cochinilla' },
    { to: '/cuenta/cupones', label: 'Cupones', value: data?.coupons_count, motif: 'ojo', dye: 'chilca' },
    { to: '/cuenta/resenas', label: 'Reseñas', value: data?.reviews_count, motif: 'sol', dye: 'solOscuro' },
  ] as const
  return (
    <>
      <PageHeader title={`Hola, ${user?.name.split(' ')[0]}`} description="Todo lo que guardaste y reservaste en Turiscovery." />
      <EmailVerification verifiedAt={data ? data.email_verified_at ?? null : user?.email_verified_at} />
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
        {cards.map((c) => (
          <StatTile key={c.to} to={c.to} value={c.value ?? '–'} label={c.label} motif={c.motif} dye={c.dye} />
        ))}
      </div>
      {(user?.role === 'socio' || user?.role === 'admin') && (
        <div className="mt-6">
        <Alert tone="info">
          Tienes una cuenta de {user.role === 'admin' ? 'administrador' : 'socio'}. Gestiona tu contenido desde el{' '}
          <Link to={user.role === 'admin' ? '/admin' : '/dashboard'} className={TEXT_LINK}>panel</Link>.
        </Alert>
        </div>
      )}
      <section className="card mt-8 flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
        <Tocapu motif="isla" dye="lago" size={40} />
        <div className="flex-1">
          <h2 className="t-h3">¿Vas al lago o a las islas?</h2>
          <p className="mt-1 text-sm text-ink-soft">Allí casi no hay señal. Guarda tus cupones, reservas y tu plan antes de salir.</p>
        </div>
        <SaveForTripButton variant="primary" label="Guardar para el viaje" />
      </section>
      <ForYou preferences={data?.preferences ?? user?.preferences} />
      <StitchRow dye="cochinilla" className="mt-8" />
      <div className="mt-8 flex flex-wrap gap-3">
        <ButtonLink to="/experiencias">Explorar experiencias</ButtonLink>
        <ButtonLink to="/eventos" variant="secondary">Ver próximos eventos</ButtonLink>
      </div>
    </>
  )
}

const PACE_DIFFICULTY: Record<TravelPace, 'easy' | 'medium' | 'hard'> = { suave: 'easy', moderado: 'medium', intenso: 'hard' }

/** Una fila de sugerencias de un tipo de contenido */
function ForYouRow({ type, pace }: { type: Interest; pace: TravelPace | null }) {
  const params = type === 'experiencias' && pace ? { per_page: 3, difficulty: PACE_DIFFICULTY[pace] } : { per_page: 3 }
  const { data, isLoading } = useContentList<AnyContent>(type, params)
  const items = data?.data ?? []
  if (!isLoading && !items.length) return null
  return (
    <div>
      <div className="flex items-baseline justify-between gap-3">
        <h3 className="t-h3">{CONTENT[type].plural}</h3>
        <Link to={`/${type}`} className={TEXT_LINK}>Ver todo</Link>
      </div>
      <div className="mt-3">
        {isLoading ? (
          <CardGridSkeleton count={3} />
        ) : (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {items.map((item) => <ContentCard key={item.id} item={item} type={type} />)}
          </div>
        )}
      </div>
    </div>
  )
}

/** Sugerencias según los intereses y el ritmo guardados en el perfil */
function ForYou({ preferences }: { preferences: UserPreferences | null | undefined }) {
  const interests = preferences?.interests ?? []
  if (!interests.length) {
    return (
      <section className="card mt-8 flex flex-col gap-3 p-5 sm:flex-row sm:items-center">
        <Tocapu motif="persona" dye="anil" size={40} />
        <p className="flex-1 text-ink-soft">Cuéntanos qué te interesa y a qué ritmo viajas para sugerirte experiencias a tu medida.</p>
        <ButtonLink to="/cuenta/perfil" variant="secondary">Elegir mis intereses</ButtonLink>
      </section>
    )
  }
  return (
    <section className="mt-8 flex flex-col gap-6" aria-labelledby="para-ti">
      <div className="flex items-baseline justify-between gap-3">
        <h2 id="para-ti" className="t-h2">Para ti</h2>
        <Link to="/cuenta/perfil" className={TEXT_LINK}>Cambiar intereses</Link>
      </div>
      {interests.slice(0, 3).map((type) => (
        <ForYouRow key={type} type={type} pace={preferences?.pace ?? null} />
      ))}
    </section>
  )
}
