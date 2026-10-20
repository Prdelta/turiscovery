import { Images, Music2, Radio } from 'lucide-react'
import { defineFeature } from '@/shared/routing/feature'
import { lazyPage } from '@/shared/routing/lazyPage'

const page = () => import('./CandelariaPage')
const live = () => import('./admin/CandelariaLiveAdmin')

/** Fiesta de la Candelaria: danzas, galería, desfile en vivo y su administración */
export const candelariaFeature = defineFeature({
  name: 'candelaria',
  site: [
    { path: '/candelaria', element: lazyPage(page, 'default') },
    { path: '/candelaria/en-vivo', element: lazyPage(() => import('./CandelariaLivePage'), 'default') },
    { path: '/candelaria/danzas/:id', element: lazyPage(page, 'DanzaDetail') },
    { path: '/candelaria/publicaciones/:id', element: lazyPage(() => import('./CandelariaPostDetail'), 'CandelariaPostDetail') },
  ],
  panels: {
    admin: [
      { path: 'candelaria-vivo', element: lazyPage(live, 'ProgramAdmin') },
      { path: 'candelaria-vivo/:id', element: lazyPage(live, 'LiveControl') },
      { path: 'danzas', element: lazyPage(() => import('./admin/DanzasAdmin'), 'DanzasAdmin') },
      { path: 'galeria', element: lazyPage(() => import('./admin/GalleryAdmin'), 'GalleryAdmin') },
    ],
  },
  nav: {
    admin: [
      { path: 'candelaria-vivo', label: 'Candelaria en vivo', order: 30, icon: Radio },
      { path: 'danzas', label: 'Danzas', order: 40, icon: Music2 },
      { path: 'galeria', label: 'Galería Candelaria', order: 50, icon: Images },
    ],
  },
})
