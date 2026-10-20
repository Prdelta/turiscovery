import { CalendarDays, Compass } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'
import { PendingBookingsBadge } from './PendingBookingsBadge'

/** Reservas: reservar una salida, las del viajero y las que recibe el socio */
export const bookingsFeature = defineFeature({
  name: 'bookings',
  site: [{ path: '/experiencias/:id/reservar', element: lazyPage(() => import('./BookingPage'), 'default') }],
  panels: {
    account: [{ path: 'reservas', element: lazyPage(() => import('./MyBookingsPage'), 'MyBookingsPage') }],
    partner: [{ path: 'reservas', element: lazyPage(() => import('./PartnerBookingsPage'), 'PartnerBookingsPage') }],
  },
  nav: {
    account: [{ path: 'reservas', label: 'Reservas', order: 20, icon: Compass }],
    partner: [{ path: 'reservas', label: 'Reservas', order: 10, icon: CalendarDays, Badge: PendingBookingsBadge }],
  },
})
