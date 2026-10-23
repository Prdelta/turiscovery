# Ñan Puno

Guía turística de Puno y el lago Titicaca. Los socios locales publican experiencias, eventos, promociones y locales. Los viajeros descubren, reservan, obtienen cupones y escuchan a los anfitriones de las comunidades.

- **Backend:** Laravel 12 (API REST) + PostgreSQL con PostGIS
- **Frontend:** React 19 + TypeScript + Vite en `frontend/`, Tailwind CSS v4, React Router, TanStack Query, Leaflet, Lucide
- **3D:** Three.js con React Three Fiber y Drei (el lago Titicaca de la portada)
- **Autenticación:** cookie de sesión de Sanctum (httpOnly + XSRF) y Google OAuth (Socialite)
- **PWA:** se instala y funciona sin conexión en el lago y las islas

---

## Puesta en marcha

### Requisitos

- PHP 8.2+ con `pdo_pgsql` y `pdo_sqlite`
- Composer
- Node.js 18+
- PostgreSQL 14+ con PostGIS (solo para producción; ver el modo local con SQLite)

### Instalación

```bash
composer install
npm install --prefix frontend
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

### Opción A: desarrollo local con SQLite (recomendada)

No necesita PostgreSQL ni PostGIS, y no toca ninguna base remota. Sin PostGIS, la columna `location` se guarda como texto y la búsqueda por cercanía usa `latitude`/`longitude` (ver `app/Models/Concerns/HasLocation.php`).

```bash
touch database/local.sqlite
DB_CONNECTION=sqlite DB_DATABASE="$(pwd)/database/local.sqlite" php artisan migrate --seed
```

En PowerShell:

```powershell
New-Item database/local.sqlite -ItemType File -Force
$env:DB_CONNECTION='sqlite'; $env:DB_DATABASE="$PWD/database/local.sqlite"; php artisan migrate --seed
```

Luego levanta la API con esas mismas variables, más `SESSION_DRIVER=file` y `CACHE_STORE=file`, y en otra terminal levanta el frontend:

```bash
DB_CONNECTION=sqlite DB_DATABASE="$(pwd)/database/local.sqlite" SESSION_DRIVER=file CACHE_STORE=file php artisan serve
npm --prefix frontend run dev
```

En la app de escritorio de Claude Code, `.claude/launch.json` ya trae las configuraciones `backend` (con esas variables) y `frontend`.

### Opción B: PostgreSQL con PostGIS

Configura `DB_*` en `.env` y corre:

```bash
php artisan migrate --seed
composer dev   # API + cola + frontend juntos
```

> **Cuidado:** `migrate:fresh`, `db:seed` y los tests vacían o llenan la base a la que apunte la configuración. Antes de correrlos, confirma a qué base apunta `.env` y que no exista `bootstrap/cache/config.php` (`php artisan config:clear`).

### Direcciones

- Frontend: http://localhost:5173
- API: http://localhost:8000/api. Vite reenvía `/api`, `/sanctum` y `/storage` a Laravel; las rutas web de Laravel redirigen al frontend.

### Usuarios de prueba (seeder)

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Viajero | turista@example.com | password123 |
| Socio | socio@example.com | password123 |
| Admin | admin@example.com | password123 |

Son solo para desarrollo. No corras el seeder en producción.

### Variables de entorno importantes

```env
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173,localhost,127.0.0.1,localhost:8000,127.0.0.1:8000

# Google OAuth: sin estas dos, el botón vuelve al login con un aviso
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:5173/api/auth/google/callback

# Correos (reservas, verificación, recuperar contraseña). Con "log" quedan en storage/logs
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_FROM_ADDRESS=hola@nanpuno.pe
MAIL_FROM_NAME=Ñan Puno
```

Para Google OAuth, crea un cliente "Web application" en Google Cloud Console con la URI de redirección `http://localhost:5173/api/auth/google/callback` (y la de producción).

### Tareas programadas

```bash
php artisan schedule:work   # en desarrollo; en producción, el cron de Laravel cada minuto
```

| Comando | Cuándo | Qué hace |
|---------|--------|----------|
| `nanpuno:expire` | 03:00 | Vence cupones caducados, completa reservas confirmadas de días pasados (y pide la reseña) y cancela las pendientes que nadie confirmó |
| `bookings:remind` | 17:00 hora de Puno | Recuerda al viajero sus salidas confirmadas del día siguiente |

Ambos aceptan `--dry-run`.

---

## Pruebas

Las pruebas corren en SQLite en memoria (`phpunit.xml`). No necesitan PostGIS ni tocan ninguna base real.

```bash
php artisan config:clear
php artisan test
```

El workflow `.github/workflows/ci.yml` también las corre contra PostgreSQL + PostGIS, junto con el lint y el build del frontend. Para replicarlo en local: `docker compose up -d` (PostGIS en el puerto 5433) y

```bash
DB_CONNECTION=pgsql DB_HOST=127.0.0.1 DB_PORT=5433 DB_DATABASE=nanpuno_test DB_USERNAME=nanpuno DB_PASSWORD=nanpuno php artisan test
```

Frontend:

```bash
npm --prefix frontend run lint
npm --prefix frontend run build
```

---

## Arquitectura

### Backend

| Capa | Dónde | Para qué |
|------|-------|----------|
| Rutas | `routes/api.php` | Públicas, de usuario, de socio (`role:socio,admin`) y de admin (`role:admin`) |
| Validación | `app/Http/Requests` | Reglas de crear y editar cada tipo de contenido |
| Autorización | `app/Policies` | Quién edita o borra cada registro (`OwnedContentPolicy`: dueño o admin) |
| Lógica de dominio | `app/Services` | `BookingService` (cupos sin sobreventa, transiciones de estado) y `CandelariaLive` (desfile en vivo) |
| Avisos | `app/Notifications` | `BookingNotification`: aviso en la app (siempre) y correo (según las preferencias del usuario) |
| Modelos | `app/Models` + `Concerns` | `BelongsToOwner`, `HasImages`, `HasLocation`, `Reviewable` |
| Tipos polimórficos | `app/Support/MorphTypes.php` | Alias estables (`experiencia`, `evento`…) en reseñas y favoritos |
| Errores | `bootstrap/app.php` | Toda respuesta de error es `{ success: false, message, errors? }`, en español |

### Frontend (`frontend/src`)

- `app/`: router, proveedores y layouts. `app/features.ts` registra los dominios: cada uno aporta sus rutas y su menú.
- `features/<dominio>/`: explore (portada 3D, mapa, búsqueda), catalog, experiences, events, promotions, locales, bookings, candelaria, hosts, almanac, planner, trip, account, partner, admin, auth, notifications, info.
- `shared/`: cliente de la API y tipos, autenticación, UI base, identidad textil (`textile/Weave.tsx`), mapa, modo sin conexión y configuración (`config/content.ts`, `config/places.ts`).

---

## Funciones

### Para el viajero

- **El lago es la interfaz:** la portada es el Titicaca en 3D con sus lugares (Puno, Uros, Taquile, Amantaní, Sillustani, Chucuito). Cada contenido se asigna al lugar más cercano a sus coordenadas.
- **Reservas** con cupos por fecha, confirmación del socio y avisos en la app y por correo.
- **Cupones:** el viajero los obtiene en cada promoción y el local los valida al cobrar.
- **Mi cuenta:** favoritos, reservas, asistencias, cupones, reseñas, foto de perfil y preferencias (intereses, ritmo de viaje y qué correos recibir). Con esas preferencias se arma la sección "Para ti".
- **Planificador con aclimatación** (`/planificador`): reparte las actividades según la altura (3 812 m) y el lugar de origen. El plan vive en la URL y se guarda en el dispositivo.
- **Candelaria en vivo** (`/candelaria/en-vivo`): qué conjunto está pasando, los siguientes y su hora estimada, con aviso para las danzas marcadas.
- **Anfitriones con voz** (`/anfitriones`): retrato, historia, idiomas y saludo grabado en su lengua.
- **Almanaque vivo** (`/hoy`): fiestas del calendario andino, fase lunar, época del año y viento en el lago (Open-Meteo).
- **Sin conexión:** "Guardar para el viaje" descarga cupones, reservas y el plan. Al cerrar sesión se borran del dispositivo.
- **Cuenta:** registro con verificación de correo (no bloquea el uso), recuperación de contraseña (`/recuperar`) e inicio de sesión con Google.

### Para el socio (`/dashboard`)

- Publicar y editar experiencias, eventos, promociones, locales y contenido de la Candelaria, con imágenes y ubicación en el mapa.
- Recibir reservas, confirmarlas o rechazarlas, y marcarlas como completadas desde el día de la salida.
- **Canjear cupones** (`/dashboard/cupones`): buscar el código que muestra el viajero y marcarlo como usado.
- Gestionar anfitriones y grabar su saludo desde el navegador.

### Para el administrador (`/admin`)

Usuarios y socios, danzas, galería histórica, búsqueda de recursos (Wikipedia/Commons), almanaque y control del desfile en vivo de la Candelaria.

---

## Roles

| Rol | Puede |
|-----|-------|
| Viajero (`tourist`) | Ver todo lo público, reservar, obtener cupones, guardar favoritos y escribir reseñas |
| Socio (`socio`) | Lo anterior, más gestionar su propio contenido, sus reservas, sus cupones y sus anfitriones |
| Admin (`admin`) | Todo, incluido el contenido de cualquier socio |

El registro siempre crea viajeros. Las cuentas de socio las crea un administrador.

---

## API (resumen)

Todas las rutas van bajo `/api`. Las protegidas usan la cookie de sesión de Sanctum: primero `GET /sanctum/csrf-cookie`, luego enviar el encabezado `X-XSRF-TOKEN`.

| Área | Rutas |
|------|-------|
| Sesión | `POST /session/login`, `POST /session/logout`, `GET /session/check`, `POST /register` |
| Contraseña | `POST /forgot-password`, `POST /reset-password` |
| Verificación | `GET /email/verify/{id}/{hash}` (enlace firmado), `POST /email/verification-notification` |
| Contenido | `GET /experiencias`, `/eventos`, `/promociones`, `/locales`, `/candelaria` (con `?lat=&lng=&radius=` para buscar cerca) |
| Perfil | `GET/PUT /profile`, `POST /profile/avatar` |
| Reservas | `GET/POST /bookings`, `POST /bookings/{id}/cancel`, `GET /experiencias/{id}/availability` |
| Cupones | `GET/POST /user-coupons`; socio: `GET /partner/coupons`, `GET /partner/coupons/lookup?code=`, `POST /partner/coupons/redeem` |
| Avisos | `GET /notifications`, `POST /notifications/{id}/read`, `POST /notifications/read-all` |
| Socio | `GET /my/{tipo}`, `GET/PUT /partner/bookings`, `POST /uploads`, `/anfitriones` |

---

## Producción

1. `npm --prefix frontend run build` genera `frontend/dist/`. Publícalo en un hosting estático y haz que todas las rutas sirvan `index.html`.
2. Si el frontend y la API están en dominios distintos: `VITE_API_URL` al compilar, `FRONTEND_URL` y `SANCTUM_STATEFUL_DOMAINS` con el dominio del frontend, y `SESSION_DOMAIN=.tu-dominio.com`.
3. Configura SMTP, las credenciales de Google y el cron de `schedule:run`.
4. Por rendimiento, si la base está lejos del servidor, usa `SESSION_DRIVER`/`CACHE_STORE` en `redis` o `file`: con `database`, cada petición suma varias consultas extra.

---

## Licencia

MIT
