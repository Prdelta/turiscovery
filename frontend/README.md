# Turiscovery · Frontend React

SPA de Turiscovery. Consume la API Laravel del directorio superior.

```bash
npm install
npm run dev      # http://localhost:5173 (reenvía /api, /sanctum y /storage a http://127.0.0.1:8000)
npm run build    # genera dist/
npm run lint     # oxlint, incluye los límites entre capas
```

- `public/models/manifest.json`: lista de modelos del visor. `node scripts/generate-models.mjs` regenera los de ejemplo.
- `VITE_API_URL`: URL de la API cuando no está en el mismo origen (producción). Ver `.env.example`.

## Arquitectura

El código se organiza por dominio, en tres capas. Los imports usan el alias `@/` (= `src/`).

```
src/
  main.tsx              arranque
  app/                  composición: router, proveedores, cliente de datos, layouts, estilos
    features.ts         registro de features y menús de los paneles
  features/<dominio>/   todo lo de un dominio: páginas, componentes, api.ts y routes.tsx
  shared/               base sin dominio: ui, textile (guía Telar), api, auth, config, lib, map, offline, routing
```

**Reglas de dependencia** (las verifica `npm run lint`):

- `shared/` no importa de `features/` ni de `app/`.
- `features/` no importa de `app/`. Entre features se usan solo sus archivos públicos (`api.ts`, componentes exportados).
- `app/` puede importar de todo.

### Una feature

```
features/bookings/
  api.ts                claves, consultas (queryOptions) y mutaciones del dominio
  routes.tsx            rutas públicas, rutas de cada panel y entradas de menú
  BookingPage.tsx       una pantalla por archivo
  MyBookingsPage.tsx
  PartnerBookingsPage.tsx
  PendingBookingsBadge.tsx
```

**Datos.** Cada `api.ts` define claves jerárquicas con raíz propia (`['bookings', …]`, `['catalog', tipo, …]`)
y fábricas `queryOptions`. Las páginas usan `useQuery(bookingQueries.mine(page))` y nunca escriben claves a mano.
Para refrescar, se invalida la raíz del dominio: `invalidateQueries({ queryKey: bookingKeys.all })`.
Las mutaciones con efectos en otros dominios (por ejemplo, reservar cambia cupos y los contadores de la cuenta)
viven como hooks en el `api.ts` (`useCreateBooking`) para que esa lógica no se repita en cada pantalla.

**Rutas y menús.** Cada `routes.tsx` exporta un `defineFeature({ site, panels, nav })`:

- `site`: rutas absolutas dentro del sitio público.
- `panels.account | partner | admin`: rutas relativas a `/cuenta`, `/dashboard` y `/admin`.
- `nav`: entradas de menú con `order` (en saltos de 10), ícono o motivo tejido y un `Badge` opcional (contador).

`app/router.tsx` y `app/layouts/PanelLayout.tsx` arman todo a partir de `app/features.ts`.

### Agregar un dominio

1. Crear `src/features/<dominio>/` con sus páginas y su `api.ts`.
2. Crear `routes.tsx` con `defineFeature({ … })`.
3. Sumarlo a `FEATURES` en `src/app/features.ts`.

No hace falta tocar el router ni los layouts. Las pantallas se cargan en diferido con `lazyPage()`,
así cada una queda en su propio chunk.
