# Turiscovery - Tourism Platform for Puno, Peru

Turiscovery es una plataforma web que conecta a turistas con la oferta local de Puno, Perú. Se enfoca en 4 pilares fundamentales: **Candelaria**, **Experiencias**, **Eventos**, y **Promociones**.

## Stack Tecnológico

- **Backend**: Laravel 12 + PostgreSQL con PostGIS
- **Frontend**: Laravel Blade + Tailwind CSS v4 + Leaflet (OpenStreetMap)
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

# Instalar dependencias Node.js
npm install

# Ejecutar migraciones (esto habilitará PostGIS automáticamente)
php artisan migrate

# Opcional: Generar datos de prueba
php artisan db:seed
```

### 7. Iniciar Aplicación

```powershell
# Opción 1: Modo desarrollo (backend + frontend + queue)
composer dev

# Opción 2: Iniciar servicios por separado
# Terminal 1: Backend
php artisan serve

# Terminal 2: Frontend (Vite)
npm run dev

# Terminal 3: Queue worker (opcional)
php artisan queue:listen
```

La aplicación estará disponible en:
- **Backend API**: http://localhost:8000/api
- **Frontend**: http://localhost:8000

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

## Próximos Pasos

1. **Frontend Development**:
   - Páginas Blade para los 4 pilares
   - Mapa interactivo con Leaflet
   - Partner dashboard

2. **Features Adicionales**:
   - Sistema de reseñas y favoritos
   - Búsqueda global
   - Notificaciones

3. **Deployment**:
   - Configurar servidor de producción
   - CI/CD con GitHub Actions
   - Backups automáticos de base de datos

---

## Soporte

Para dudas o problemas:
- Revisar la documentación de Laravel: https://laravel.com/docs
- PostGIS documentation: https://postgis.net/docs/
- Google OAuth setup: https://developers.google.com/identity/protocols/oauth2

---

## Licencia

MIT License
