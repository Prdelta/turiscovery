import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

const pages = () => import('./HostsPages')

/** Anfitriones con voz: directorio, ficha y gestión del socio */
export const hostsFeature = defineFeature({
  name: 'hosts',
  site: [
    { path: '/anfitriones', element: lazyPage(pages, 'default') },
    { path: '/anfitriones/:id', element: lazyPage(pages, 'HostDetail') },
  ],
  panels: { partner: [{ path: 'anfitriones', element: lazyPage(() => import('./admin/HostsManager'), 'default') }] },
  nav: { partner: [{ path: 'anfitriones', label: 'Anfitriones', order: 60, motif: ['persona', 'qolle'] }] },
})
