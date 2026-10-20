import { CalendarDays } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

/** Eventos: ficha con asistencia y los eventos a los que va el viajero */
export const eventsFeature = defineFeature({
  name: 'events',
  site: [{ path: '/eventos/:id', element: lazyPage(() => import('./EventoDetail'), 'EventoDetail') }],
  panels: { account: [{ path: 'eventos', element: lazyPage(() => import('./AttendancesPage'), 'AttendancesPage') }] },
  nav: { account: [{ path: 'eventos', label: 'Eventos', order: 30, icon: CalendarDays }] },
})
