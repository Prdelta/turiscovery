import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

const pages = () => import('./StaticPages')

/** Páginas informativas y la guía de estilo */
export const infoFeature = defineFeature({
  name: 'info',
  site: [
    { path: '/sobre-nosotros', element: lazyPage(pages, 'AboutPage') },
    { path: '/contacto', element: lazyPage(pages, 'ContactPage') },
    { path: '/terminos', element: lazyPage(pages, 'TermsPage') },
    { path: '/privacidad', element: lazyPage(pages, 'PrivacyPage') },
    { path: '/guia-de-estilo', element: lazyPage(() => import('./StyleGuidePage'), 'default') },
  ],
})
