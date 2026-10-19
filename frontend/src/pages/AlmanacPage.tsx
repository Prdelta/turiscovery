import { clsx } from 'clsx'
import { useMemo, useState } from 'react'
import { AlmanacRow, MoonGlyph, TodayPanel } from '../components/Almanac'
import { Tocapu, WovenBand } from '../components/textile/Weave'
import { ErrorState, Spinner } from '../components/ui/Feedback'
import { altiplanoSeason, happensOn, localDateKey, moonInfo, useAlmanac } from '../lib/almanac'
import { parseApiError } from '../lib/api'
import { useDocumentTitle } from '../lib/hooks'

const RANGES = [
  { days: 7, label: 'Esta semana' },
  { days: 30, label: 'Próximos 30 días' },
]

export default function AlmanacPage() {
  useDocumentTitle('Almanaque')
  const [days, setDays] = useState(7)
  const { data, isLoading, isError, error, refetch } = useAlmanac(days)

  const calendar = useMemo(() => {
    const start = new Date()
    return Array.from({ length: days }, (_, i) => {
      const day = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i)
      return { day, items: (data?.items ?? []).filter((it) => happensOn(it, day)) }
    })
  }, [data, days])

  const dayFmt = new Intl.DateTimeFormat('es-PE', { weekday: 'long', day: 'numeric', month: 'long' })

  return (
    <div className="container-page py-12 sm:py-16">
      <header className="mb-10">
        <div className="flex items-center gap-4">
          <Tocapu motif="sol" dye="qolle" size={48} />
          <h1 className="text-4xl font-extrabold sm:text-5xl">Almanaque de Puno</h1>
        </div>
        <p className="prose-measure mt-4 text-lg text-ink-soft">
          Qué pasa cada día: fiestas, rituales andinos, eventos y la Candelaria, con la luna y la época del año. En el altiplano el calendario
          se vive.
        </p>
        <WovenBand motif="sol" dye="qolle" height={12} className="mt-6 rounded-sm" />
      </header>

      <div className="grid gap-10 lg:grid-cols-[1fr_360px]">
        <section aria-label="Calendario">
          <div role="group" aria-label="Rango" className="mb-6 flex gap-2">
            {RANGES.map((r) => (
              <button
                key={r.days}
                onClick={() => setDays(r.days)}
                aria-pressed={days === r.days}
                className={clsx('rounded-full border px-4 py-2 text-sm font-semibold', days === r.days ? 'border-qolle bg-qolle text-ink' : 'border-line bg-white')}
              >
                {r.label}
              </button>
            ))}
          </div>
          {isError && (
            <div className="mb-4">
              <ErrorState message={`No pudimos traer las fiestas y eventos. ${parseApiError(error).message}`} onRetry={() => refetch()} />
            </div>
          )}
          {isLoading ? (
            <Spinner />
          ) : (
            <ol className="flex flex-col gap-3">
              {calendar.map(({ day, items }, i) => {
                const moon = moonInfo(day)
                return (
                  <li key={localDateKey(day)} className={clsx('grid gap-3 rounded-[var(--radius-card)] p-4 sm:grid-cols-[180px_1fr]', i === 0 ? 'bg-white ring-2 ring-qolle' : 'bg-white')}>
                    <div className="flex items-center gap-3 sm:flex-col sm:items-start">
                      <p className="font-semibold first-letter:uppercase">{i === 0 ? 'Hoy' : dayFmt.format(day)}</p>
                      <span className="inline-flex items-center gap-1.5 text-xs text-ink-soft">
                        <MoonGlyph phase={moon.phase} size={16} /> {moon.name}
                      </span>
                    </div>
                    {items.length ? (
                      <ul className="-mx-2 flex flex-col">
                        {items.map((it) => <AlmanacRow key={`${it.source}-${it.id}-${it.starts_at}`} item={it} />)}
                      </ul>
                    ) : (
                      <p className="self-center text-sm text-ink-soft">Día tranquilo. {i === 0 ? altiplanoSeason(day).text : ''}</p>
                    )}
                  </li>
                )
              })}
            </ol>
          )}
        </section>
        <aside className="lg:sticky lg:top-24 lg:self-start">
          <TodayPanel />
          <p className="mt-4 text-xs text-ink-soft">
            El estado del lago es una estimación con datos de Open-Meteo. Antes de navegar, consulta siempre a la capitanía o a tu operador.
          </p>
        </aside>
      </div>
    </div>
  )
}
