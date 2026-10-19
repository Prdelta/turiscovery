# Turiscovery - Tourism Platform for Puno, Peru

Turiscovery es una plataforma web que conecta a turistas con la oferta local de Puno, Perú. Se enfoca en 4 pilares fundamentales: **Candelaria**, **Experiencias**, **Eventos**, y **Promociones**.

## Stack Tecnológico

- **Backend**: Laravel 12 + PostgreSQL con PostGIS
- **Frontend**: React 19 + TypeScript + Vite (carpeta `frontend/`), Tailwind CSS v4, React Router, TanStack Query, Leaflet (OpenStreetMap), Lucide Icons
- **3D**: Three.js con React Three Fiber y Drei (escena del lago Titicaca generada por código y visor de modelos `.glb`)
- **Autenticación**: Laravel Sanctum + Google OAuth (Socialite)
- **Seguridad**: RBAC (Role-Based Access Control)

---

## Configuración Inicial

### 1. Requisitos Previos

- PHP 8.2+
- Composer
- PostgreSQL 14+ **con extensión PostGIS**
- Node.js 18+
- npm

### 2. Instalación de PostGIS

**Windows (con PostgreSQL instalado):**
```powershell
# Verificar si PostGIS está disponible
psql -U postgres -c "SELECT * FROM pg_available_extensions WHERE name = 'postgis';"
```

Si PostGIS no está instalado, descargarlo desde:
- https://postgis.net/install/
- O usar Stack Builder (incluido con PostgreSQL para Windows)

**Linux/macOS:**
```bash
# Ubuntu/Debian
sudo apt-get install postgis postgresql-14-postgis-3

# macOS con Homebrew
brew install postgis
```

### 3. Configurar Base de Datos

```powershell
# Crear base de datos
psql -U postgres
CREATE DATABASE turiscovery;
\q
```

### 4. Configurar Credenciales de Google OAuth

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita **Google+ API**
4. Ve a **Credentials** → **Create Credentials** → **OAuth 2.0 Client ID**
5. Tipo de aplicación: **Web application**
6. **URIs de redirección autorizados**:
   - `http://localhost:8000/api/auth/google/callback`
   - `http://localhost:3000/api/auth/google/callback` (si usas frontend separado)
7. Copia el **Client ID** y **Client Secret**

### 5. Configurar Variables de Entorno

Edita el archivo `.env`:

```env
# Base de datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=turiscovery
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Google OAuth
GOOGLE_CLIENT_ID=tu_client_id_de_google
GOOGLE_CLIENT_SECRET=tu_secret_de_google
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:8000
```

### 6. Instalar Dependencias y Ejecutar Migraciones

```powershell
# Instalar dependencias PHP
composer install

# Instalar dependencias del frontend React
npm install --prefix frontend

# Ejecutar migraciones (habilita PostGIS y copia coordenadas a columnas numéricas)
php artisan migrate

# Enlace público para las imágenes que suben los socios
php artisan storage:link

# Datos de prueba. Define SEED_USER_PASSWORD en .env o el seeder genera una
# contraseña aleatoria y la muestra en la consola.
php artisan db:seed
```

### 7. Iniciar Aplicación

```powershell
# Opción 1: todo junto (API + cola + frontend React)
composer dev

# Opción 2: por separado
# Terminal 1: API Laravel
php artisan serve
# Terminal 2: Frontend React (Vite, reenvía /api, /sanctum y /storage a Laravel)
npm --prefix frontend run dev
```

Variables necesarias en `.env` para la SPA:

```env
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173,localhost,127.0.0.1,127.0.0.1:8000,localhost:8000
GOOGLE_REDIRECT_URI=http://localhost:5173/api/auth/google/callback
```

La aplicación estará disponible en:
- **Frontend React**: http://localhost:5173
- **API Laravel**: http://localhost:8000/api (las rutas web de Laravel redirigen al frontend)

### Arquitectura

- `frontend/`: SPA en React. Se autentica con la cookie de sesión de Sanctum (httpOnly + XSRF), sin tokens en `localStorage`.
- `app/Http/Controllers/Api`: API REST que consume la SPA. Laravel ya no renderiza vistas Blade.
- `frontend/src/three/`: escena 3D del lago (`LakeScene.tsx`) y visor de modelos (`ModelViewer.tsx`).
- Modelos 3D: copia archivos `.glb` a `frontend/public/models/` y regístralos en `manifest.json`. Los modelos de ejemplo se regeneran con `node frontend/scripts/generate-models.mjs`.

### Estructura del back

| Capa | Dónde | Para qué |
|------|-------|----------|
| Rutas | `routes/api.php` | Endpoints públicos, de usuario, de socio (`role:socio,admin`) y de admin (`role:admin`) |
| Validación | `app/Http/Requests/Content/*Request.php` | Reglas de crear/editar cada tipo de contenido. Al editar, autorizan antes de validar |
| Autorización | `app/Policies/*Policy.php` | Quién puede editar o eliminar cada registro. `OwnedContentPolicy`: dueño o admin |
| Reglas propias | `app/Rules/OwnedByUser.php` | Un socio solo puede asociar sus propios locales y anfitriones |
| Modelos | `app/Models` + `app/Models/Concerns` | Traits compartidos: `BelongsToOwner`, `HasImages`, `HasLocation` (búsqueda por cercanía con parámetros enlazados), `Reviewable` |
| Tipos polimórficos | `app/Support/MorphTypes.php` | Alias estables (`experiencia`, `evento`…) en reseñas, favoritos y tokens. La API acepta también el nombre de clase antiguo |
| Errores | `bootstrap/app.php` | Toda respuesta de error de la API tiene la forma `{ success: false, message, errors? }` en español |
| Idioma | `lang/es` | Mensajes de validación en español (`APP_LOCALE=es`) |
| Lógica de dominio | `app/Services` | Por ahora `CandelariaLive` (estado del desfile) |

**Pruebas**: `docker compose up -d` levanta PostgreSQL + PostGIS en el puerto 5433 con una base `turiscovery_test`. Luego:

```bash
DB_CONNECTION=pgsql DB_HOST=127.0.0.1 DB_PORT=5433 DB_DATABASE=turiscovery_test DB_USERNAME=turiscovery DB_PASSWORD=turiscovery php artisan test
```

El workflow `.github/workflows/ci.yml` corre migraciones y pruebas del back con PostGIS, y el lint y el build del frontend, en cada push.

**Hoja de ruta de la reestructuración**

1. ✅ Bases: Form Requests, Policies, traits de modelos, alias polimórficos, errores y mensajes en español, Docker y CI.
2. Datos y rendimiento: una sola fuente para las coordenadas (columnas generadas desde PostGIS), calificación promedio guardada en cada contenido, búsqueda con índices `pg_trgm`/texto completo, cupos de reserva con bloqueo, Redis para caché/sesiones/colas y PgBouncer en lugar de conexiones persistentes.
3. Escala horizontal: archivos en S3/R2 con miniaturas generadas en cola, notificaciones (correo/WhatsApp) en cola, `/api/v1` con API Resources, CDN y monitoreo (Sentry, Pulse).
4. Organización por dominios (catálogo, reservas, Candelaria, comunidad, identidad) y rol de organizador para la Candelaria.

### Identidad: lo que hace distinto a Turiscovery

- **El lago es la interfaz**: la portada es el Titicaca en 3D con seis lugares (Puno, los Uros, Taquile, Amantaní, Sillustani y Chucuito). Al tocar uno, la cámara vuela hasta él y un panel muestra lo publicado ahí. Cada contenido se asigna a su lugar por cercanía a las coordenadas reales (`frontend/src/lib/places.ts`). La escena está en `frontend/src/three/LakeScene.tsx`.
- **Identidad textil**: cada tipo de contenido tiene un motivo tejido (tocapu) dibujado como una cuadrícula de 8 × 8 puntadas y un color de tinte natural: experiencias = olas en añil, eventos = chakana en cochinilla, promociones = rombo en q'olle, locales = llave escalonada en chilca y Candelaria = estrella en morado. Componentes en `frontend/src/components/textile/Weave.tsx`.
- **Almanaque vivo** (`/hoy` y el panel "Hoy en Puno" de la portada): fiestas y rituales que se repiten cada año (tabla `almanac_entries`, editable en *Administración → Almanaque*), eventos de los socios, programa de la Candelaria, fase de la luna vista desde el hemisferio sur, época del año en el altiplano y viento en el lago con las mejores horas para navegar (datos de Open-Meteo, sin clave). El `AlmanacSeeder` trae seis fechas conocidas; revísalas cada año.
- **Anfitriones con voz** (`/anfitriones`): cada experiencia puede presentar a la persona que recibe al viajero, con su retrato, su historia, los idiomas que habla y un saludo grabado en su idioma. El socio los gestiona en *Panel → Anfitriones* y puede grabar el saludo desde el navegador. Tabla `anfitriones` y columna `experiencias.anfitrion_id`.

### Funciones propias de Puno

- **Planificador con aclimatación** (`/planificador`): reparte las experiencias y eventos elegidos según la altura (3 812 m). El día de llegada solo propone actividades fáciles y cortas; las exigentes, desde el día 3 (o el 2 si el viajero viene de otra ciudad alta). El plan vive en la URL (se comparte por WhatsApp) y se guarda en el dispositivo; al reservar una actividad exigente para el día de llegada, la ficha avisa. Lógica en `frontend/src/lib/altitude.ts`.
- **Candelaria en vivo** (`/candelaria/en-vivo`): programa de la festividad y desfile en tiempo real: conjunto que está pasando, próximos conjuntos y hora estimada según el ritmo real. El visitante marca sus danzas y recibe un aviso cuando están a tres conjuntos o menos. Los administradores lo controlan desde *Administración → Candelaria en vivo*: crean las jornadas, dibujan el recorrido, pegan la lista de conjuntos y pulsan "Pasó, sigue el siguiente". Tablas `candelaria_program_items` y `candelaria_performances`; lógica en `app/Services/CandelariaLive.php`.
- **App instalable y sin conexión**: el frontend es una PWA. Al instalarla guarda la app, las fotos y los modelos 3D; las respuestas de la API y los mosaicos del mapa ya vistos quedan disponibles sin señal (lago e islas). "Guardar para usar sin conexión" (en Mi cuenta y en el planificador) descarga cupones, reservas y el plan antes de salir. Al cerrar sesión se borran del dispositivo los datos personales guardados. Íconos: `npx pwa-assets-generator` dentro de `frontend/` a partir de `public/icon.svg`.

### Producción

1. `npm --prefix frontend run build` genera `frontend/dist/`, que se puede publicar en cualquier hosting estático (Vercel, Netlify, Nginx). Configura el hosting para que todas las rutas sirvan `index.html`.
2. Si el frontend y la API están en dominios distintos, define `VITE_API_URL=https://api.tu-dominio.com` al compilar, `FRONTEND_URL` y `SANCTUM_STATEFUL_DOMAINS` con el dominio del frontend, y `SESSION_DOMAIN=.tu-dominio.com` para compartir la cookie entre subdominios.

---

## Estructura de la API

### Autenticación

#### Registro
```bash
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "role": "tourist"  // o "socio"
}
```

#### Login
```bash
POST /api/login
Content-Type: application/json

{
  "email": "juan@example.com",
  "password": "Password123!"
}

# Respuesta:
{
  "success": true,
  "data": {
    "user": {...},
    "access_token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

#### Google OAuth
```bash
# 1. Redirigir al usuario a Google
GET /api/auth/google

# 2. Google redirige de vuelta con el código
GET /api/auth/google/callback?code=...
```

### Los 4 Pilares

#### Candelaria
```bash
GET /api/candelaria?category=dance&featured=true
GET /api/candelaria/{id}
POST /api/candelaria  # Requiere auth + role:socio,admin
PUT /api/candelaria/{id}
DELETE /api/candelaria/{id}
```

#### Experiencias
```bash
GET /api/experiencias?difficulty=medium&max_price=100
GET /api/experiencias?lat=-15.8402&lng=-70.0219&radius=5000  # Cerca de Puno
POST /api/experiencias  # Requiere auth + role:socio,admin
```

#### Eventos
```bash
GET /api/eventos?category=concert&status=upcoming
GET /api/eventos?lat=-15.8402&lng=-70.0219  # Eventos cercanos
POST /api/eventos  # Requiere auth + role:socio,admin
```

**Nota:** Los eventos expirados (end_time < now) se filtran automáticamente.

#### Promociones
```bash
GET /api/promociones?type=2x1
GET /api/promociones?locale_id=5  # Promociones de un local específico
POST /api/promociones  # Requiere auth + role:socio,admin
```

**Nota:** Las promociones expiradas se filtran automáticamente.

#### Locales (Venues)
```bash
GET /api/locales?category=restaurant&lat=-15.8402&lng=-70.0219&radius=2000
POST /api/locales  # Crear venue con geolocalización
{
  "name": "Restaurante La Casona",
  "description": "Cocina típica puneña",
  "address": "Jr. Lima 123, Puno",
  "latitude": -15.8402,
  "longitude": -70.0219,
  "category": "restaurant"
}
```

### Uso de Tokens (Sanctum)

```bash
# Incluir el token en todas las requests protegidas
GET /api/me
Authorization: Bearer 1|abc123...
```

---

## Roles y Permisos (RBAC)

| Role     | Permisos                                    |
|----------|---------------------------------------------|
| **Tourist** | Ver contenido público, crear reseñas, favoritos |
| **Socio**   | Todo lo de Tourist + Crear/editar/borrar su propio contenido (locales, eventos, promociones) |
| **Admin**   | Acceso total, puede editar cualquier contenido |

### Autorización en Controllers

Los controllers verifican automáticamente que:
- Los socios solo pueden editar/borrar su propio contenido
- Los admins pueden editar/borrar cualquier contenido

---

## Geolocalización con PostGIS

### Validación de Coordenadas

Las coordenadas se validan para estar dentro de la región de Puno:
- **Latitud**: entre -16.5 y -14.5
- **Longitud**: entre -71 y -69

### Búsqueda por Proximidad

```bash
# Encontrar locales a 5km de las coordenadas del centro de Puno
GET /api/locales?lat=-15.8402&lng=-70.0219&radius=5000

# La respuesta incluirá un campo "distance" en metros
```

---

## Filtrado de Contenido Time-Sensitive

### Eventos

Los eventos se filtran automáticamente usando el scope `active()`:
- `is_active = true`
- `end_time > now()`

Los eventos expirados se mantienen en la base de datos para análisis histórico.

### Promociones

Las promociones se filtran con:
- `is_active = true`
- `end_date > now()`
- `start_date <= now()`

---

## Seguridad

### Hashing de Contraseñas

Laravel usa **bcrypt** por defecto (configurable a Argon2 en `config/hashing.php`).

### Rate Limiting

El login tiene rate limiting de **5 intentos por minuto por IP**.

### Validación de Inputs

Todos los controllers usan `Validator` para prevenir:
- SQL Injection
- XSS
- Datos malformados

### HTTPS/SSL

**En producción**, configurar HTTPS:
1. Obtener certificado SSL (Let's Encrypt)
2. En `AppServiceProvider`:
```php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

---

## Partner Dashboard

Los socios pueden:

1. **Crear Locales** con mapa interactivo:
   - Arrastrar marcador en el mapa
   - Las coordenadas se capturan automáticamente
   - Se valida que estén en Puno

2. **Gestionar Eventos y Promociones**:
   - Crear eventos con fechas de inicio/fin
   - Crear promociones con tipos: 2x1, porcentaje, monto fijo

3. **Ver Analytics** (TODO):
   - Vistas
   - Reseñas
   - Favoritos

---

## Testing

```powershell
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter AuthenticationTest
php artisan test --filter GeolocationTest
```

---

## Estado Actual y Próximos Pasos

1.  **Frontend Development (Completado)**:
    -   ✅ Diseño Premium con Tailwind CSS v4
    -   ✅ Páginas Blade para los 4 pilares (Candelaria, Experiencias, Eventos, Promociones)
    -   ✅ Panel de Login/Registro mejorado

2.  **Gestión de Contenido (En Progreso)**:
    -   ✅ Generación automática de imágenes contextuales
    -   ✅ Seeders actualizados para demostración
    -   ⬜ Dashboard de socios avanzado

3.  **Deployment (Pendiente)**:
    -   Configurar servidor de producción
    -   CI/CD con GitHub Actions
    -   Backups automáticos de base de datos

---

## Soporte

Para dudas o problemas:
- Revisar la documentación de Laravel: https://laravel.com/docs
- PostGIS documentation: https://postgis.net/docs/
- Google OAuth setup: https://developers.google.com/identity/protocols/oauth2

---

## Licencia

MIT License
