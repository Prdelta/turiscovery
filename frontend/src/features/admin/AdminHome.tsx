import { useQuery } from '@tanstack/react-query'
import { Plus } from 'lucide-react'
import { Link } from 'react-router'
import { PageHeader } from '@/shared/ui/PageHeader'
import { ButtonLink } from '@/shared/ui/Button'
import type { Dye, Motif } from '@/shared/textile/Weave'
import { ErrorState, Spinner } from '@/shared/ui/Feedback'
import { PanelCard, StatTile } from '@/shared/ui/Panel'
import { parseApiError } from '@/shared/api/client'
import { formatDate } from '@/shared/lib/format'
import { useDocumentTitle } from '@/shared/lib/hooks'
import { adminQueries } from '@/features/admin/api'

const TILE_WEAVE: Record<string, [Motif, Dye]> = {
  Usuarios: ['persona', 'lago'],
  Viajeros: ['isla', 'lago'],
  'Socios activos': ['llave', 'qolle'],
  'Socios nuevos este mes': ['sol', 'solOscuro'],
  Locales: ['llave', 'tierra'],
  Experiencias: ['olas', 'anil'],
  Eventos: ['chakana', 'cochinilla'],
  Promociones: ['ojo', 'chilca'],
}

export function AdminHome() {
  useDocumentTitle('Supervisión')
  const { data, isLoading, isError, error, refetch } = useQuery(adminQueries.supervision())
  if (isLoading) return <Spinner />
  if (isError || !data) return <ErrorState message={parseApiError(error).message} onRetry={() => refetch()} />
  const s = data.stats
  const tiles: [string, number, string?][] = [
    ['Usuarios', s.total_usuarios],
    ['Viajeros', s.turistas],
    ['Socios activos', s.socios_activos, `${s.socios_inactivos} desactivados`],
    ['Socios nuevos este mes', s.socios_nuevos_mes],
    ['Locales', s.total_locales],
    ['Experiencias', s.total_experiencias],
    ['Eventos', s.total_eventos],
    ['Promociones', s.total_promociones],
  ]
  return (
    <>
      <PageHeader title="Supervisión" description="Estado general de usuarios, socios y contenido." action={<ButtonLink to="/admin/usuarios/nuevo" icon={<Plus className="size-4.5" />}>Crear socio</ButtonLink>} />
      <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {tiles.map(([label, value, note]) => (
          <StatTile key={label} value={value} label={label} note={note} motif={TILE_WEAVE[label]?.[0] ?? 'chakana'} dye={TILE_WEAVE[label]?.[1] ?? 'morado'} />
        ))}
      </div>
      <div className="mt-10 grid gap-6 xl:grid-cols-2">
        <PanelCard title="Socios con más contenido">
          {data.socios_mas_activos.length ? (
            <table className="w-full text-left text-sm">
              <thead className="t-label text-ink-soft">
                <tr><th className="pb-2">Socio</th><th className="pb-2 text-right">Loc.</th><th className="pb-2 text-right">Exp.</th><th className="pb-2 text-right">Evt.</th><th className="pb-2 text-right">Prom.</th></tr>
              </thead>
              <tbody className="divide-y-2 divide-line">
                {data.socios_mas_activos.map((u) => (
                  <tr key={u.id}>
                    <td className="py-2.5"><Link to={`/admin/usuarios/${u.id}`} className="font-bold hover:text-primary-dark hover:underline">{u.name}</Link></td>
                    <td className="text-right">{u.locales_count}</td>
                    <td className="text-right">{u.experiencias_count}</td>
                    <td className="text-right">{u.eventos_count}</td>
                    <td className="text-right">{u.promociones_count}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <p className="text-ink-soft">Aún no hay socios.</p>
          )}
        </PanelCard>
        <PanelCard title="Socios desactivados">
          {data.socios_inactivos.length ? (
            <ul className="divide-y-2 divide-line">
              {data.socios_inactivos.map((u) => (
                <li key={u.id} className="py-2.5 text-sm">
                  <Link to={`/admin/usuarios/${u.id}`} className="font-bold hover:text-primary-dark hover:underline">{u.name}</Link>
                  <span className="text-ink-soft"> · desde el {formatDate(u.deactivated_at)}{u.deactivated_by ? ` por ${u.deactivated_by.name}` : ''}</span>
                </li>
              ))}
            </ul>
          ) : (
            <p className="text-ink-soft">Todos los socios están activos.</p>
          )}
        </PanelCard>
      </div>
    </>
  )
}
