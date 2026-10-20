import { Link, useSearchParams } from 'react-router'
import { HostCard } from '@/features/hosts/HostVoice'
import { ButtonLink } from '@/shared/ui/Button'
import { Badge, DifficultyBadge } from '@/shared/ui/Feedback'
import { formatPrice } from '@/shared/lib/format'
import type { Experiencia } from '@/shared/api/types'
import { AsideCard, DetailShell, DetailState, Fact, LocaleLink, useDetail } from '@/features/catalog/detail/DetailShell'

export function ExperienciaDetail() {
  const { data, isLoading, error, refetch } = useDetail<Experiencia>('experiencias')
  const [params] = useSearchParams()
  if (!data) return <DetailState isLoading={isLoading} error={error} refetch={refetch} />
  const e = data
  const fecha = params.get('fecha')
  const bookHref = `/experiencias/${e.id}/reservar${fecha ? `?fecha=${fecha}` : ''}`
  const price = formatPrice(e.price_pen)

  return (
    <DetailShell
      item={e}
      type="experiencias"
      kicker={e.tags?.map((t) => <Badge key={t}>{t}</Badge>)}
      meta={<LocaleLink locale={e.locale} />}
      facts={
        <>
          <Fact label="Duración">{e.duration_hours ? `${e.duration_hours} horas` : 'A consultar'}</Fact>
          <Fact label="Dificultad">
            <DifficultyBadge level={e.difficulty} />
          </Fact>
          {(e.altitude_m || e.effort_note) && (
            <Fact label="Altura y esfuerzo" wide>
              {e.altitude_m ? `Hasta ${e.altitude_m.toLocaleString('es-PE').replace(',', ' ')} m` : ''}
              {e.altitude_m && e.effort_note ? ' · ' : ''}
              {e.effort_note && <span className="font-normal">{e.effort_note}</span>}
            </Fact>
          )}
          {(e.meeting_point || e.meeting_time) && (
            <Fact label="Punto de encuentro" wide>
              {e.meeting_point}
              {e.meeting_time && (
                <span className="font-normal text-ink-soft">
                  {e.meeting_point ? ' · ' : ''}
                  {e.meeting_time.slice(0, 5)} h
                </span>
              )}
            </Fact>
          )}
          {e.max_participants ? <Fact label="Grupo">Hasta {e.max_participants} personas</Fact> : null}
        </>
      }
      aside={
        <AsideCard>
          <div>
            <p className="font-display t-h1">{price ?? 'Precio a consultar'}</p>
            {price && price !== 'Gratis' && <p className="text-sm text-ink-soft">por persona</p>}
          </div>
          <ButtonLink to={bookHref} size="lg" fullWidth>Reservar</ButtonLink>
          <p className="text-sm text-ink-soft">No se cobra nada ahora. El socio confirma la fecha contigo.</p>
          <Link to="/planificador" className="text-sm font-bold text-primary-dark underline decoration-2 underline-offset-4">
            ¿Qué día te conviene según la altura?
          </Link>
        </AsideCard>
      }
      bottomBar={
        <>
          <p>
            <span className="block font-display t-h2 leading-none">{price ?? 'A consultar'}</span>
            {price && price !== 'Gratis' && <span className="text-sm text-ink-soft">por persona</span>}
          </p>
          <ButtonLink to={bookHref} size="lg">Reservar</ButtonLink>
        </>
      }
    >
      {e.anfitrion && (
        <div className="mt-10">
          <HostCard host={e.anfitrion} />
        </div>
      )}
      {e.content && (
        <section className="mt-10">
          <h2 className="t-h2 mb-3">Itinerario y detalles</h2>
          <div className="prose-measure whitespace-pre-line text-ink-soft">{e.content}</div>
        </section>
      )}
    </DetailShell>
  )
}
