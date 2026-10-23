import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { RouterProvider } from 'react-router'
import '@/app/index.css'
import { AppProviders } from '@/app/providers'
import { router } from '@/app/router'
import { migrateLegacyStorage } from '@/shared/offline/offline'

// Antes de montar la app: el plan y el viaje guardados con el nombre anterior pasan a las claves nuevas
migrateLegacyStorage()

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <AppProviders>
      <RouterProvider router={router} />
    </AppProviders>
  </StrictMode>,
)
