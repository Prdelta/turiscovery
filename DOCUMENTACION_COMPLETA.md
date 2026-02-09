# 📚 Documentación Completa del Sistema Turiscovery

**Versión:** 2.0
**Última actualización:** 2026-02-09
**Stack:** Laravel 12 + PostgreSQL + PostGIS + Tailwind CSS v4

---

## 📋 Índice

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Funcionalidades Implementadas](#3-funcionalidades-implementadas)
4. [Módulos del Sistema](#4-módulos-del-sistema)
5. [Panel de Administración](#5-panel-de-administración)
6. [Sistema de Autenticación y Roles](#6-sistema-de-autenticación-y-roles)
7. [Base de Datos](#7-base-de-datos)
8. [Frontend y Diseño](#8-frontend-y-diseño)
9. [Configuración y Deployment](#9-configuración-y-deployment)
10. [Pendientes y Mejoras](#10-pendientes-y-mejoras)
11. [Guía de Mantenimiento](#11-guía-de-mantenimiento)
12. [Solución de Problemas](#12-solución-de-problemas)

---

## 1. Resumen Ejecutivo

### 🎯 Objetivo del Proyecto

**Turiscovery** es una plataforma web turística para Puno, Perú, que conecta turistas con la oferta local, enfocándose en **4 pilares fundamentales**:

1. **🎭 Candelaria** - Festividad, danzas, galería histórica
2. **🧭 Experiencias** - Tours, actividades, paquetes turísticos
3. **📅 Eventos** - Conciertos, festivales, eventos culturales
4. **🎟️ Promociones** - Ofertas especiales, descuentos, paquetes

### 🏗️ Stack Tecnológico

| Componente | Tecnología | Versión |
|------------|------------|---------|
| **Backend** | Laravel | 12.x |
| **Base de Datos** | PostgreSQL + PostGIS | 14+ |
| **Frontend** | Blade Templates + Tailwind CSS | v4 |
| **JavaScript** | Vanilla JS + Axios | - |
| **Autenticación** | Laravel Sanctum + Socialite | - |
| **Mapas** | Leaflet + OpenStreetMap | - |
| **Iconos** | Lucide Icons | - |

### 📊 Estado Actual del Proyecto

| Componente | Estado | Progreso |
|------------|--------|----------|
| Backend API | ✅ Completo | 100% |
| Autenticación | ✅ Completo | 100% |
| Base de Datos | ✅ Completo | 100% |
| Panel Admin Candelaria | ✅ Completo | 100% |
| Panel Admin Socios | ✅ Completo | 100% |
| Dashboard Usuario | ✅ Completo | 100% |
| Frontend Público | 🟡 Parcial | 70% |
| Analytics | ⬜ Pendiente | 0% |
| Testing | 🟡 Parcial | 40% |
| Deployment | ⬜ Pendiente | 0% |

---

## 2. Arquitectura del Sistema

### 🏛️ Arquitectura General

```
┌─────────────────────────────────────────────────────────────┐
│                    USUARIO FINAL                            │
│  (Turistas, Socios, Administradores)                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   FRONTEND LAYER                            │
│  • Blade Templates (Server-Side Rendering)                 │
│  • Tailwind CSS v4 (Styling)                               │
│  • JavaScript (Axios para API calls)                        │
│  • Leaflet (Mapas interactivos)                            │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   BACKEND LAYER (Laravel 12)                │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │ Controllers  │  │ Middleware   │  │   Models     │    │
│  │ (API + Web)  │  │ (Auth/RBAC)  │  │ (Eloquent)   │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │  Services    │  │  Validators  │  │   Seeders    │    │
│  │  (Business)  │  │  (Security)  │  │   (Data)     │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              DATABASE LAYER (PostgreSQL + PostGIS)          │
│                                                             │
│  • Tablas principales (users, locales, eventos, etc.)      │
│  • Geolocalización (geometry columns, spatial indexes)     │
│  • Relaciones polimórficas (reviews, favorites)           │
│  • Soft deletes (datos históricos)                         │
└─────────────────────────────────────────────────────────────┘
```

### 🔐 Sistema de Autenticación

```
┌──────────────┐
│   Usuario    │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────┐
│  Métodos de Autenticación:          │
│  1. Email + Password (Laravel Auth) │
│  2. Google OAuth (Socialite)        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│     Laravel Sanctum                 │
│     (Token-based Auth)              │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│     Role-Based Access Control        │
│     (Tourist, Socio, Admin)         │
└──────────────────────────────────────┘
```

---

## 3. Funcionalidades Implementadas

### ✅ Backend Completo

#### Autenticación y Autorización
- ✅ Registro de usuarios (email/password)
- ✅ Login con email/password
- ✅ Login con Google OAuth
- ✅ Sistema de roles (Tourist, Socio, Admin)
- ✅ Middleware RBAC personalizado
- ✅ Protección de rutas por rol
- ✅ Rate limiting en login (5 intentos/minuto)
- ✅ Tokens Sanctum para API

#### API RESTful - Los 4 Pilares

**1. Candelaria**
- ✅ CRUD completo de danzas
- ✅ Galería de fotos históricas
- ✅ Filtrado por tipo (mestiza/autóctona)
- ✅ Gestión de danzas destacadas
- ✅ Buscador de recursos (imágenes + Wikipedia)

**2. Experiencias**
- ✅ CRUD completo
- ✅ Búsqueda por geolocalización
- ✅ Filtrado por dificultad y precio
- ✅ Relación con locales

**3. Eventos**
- ✅ CRUD completo
- ✅ Filtrado automático de eventos expirados
- ✅ Búsqueda por fecha y ubicación
- ✅ Categorías (concert, festival, cultural)

**4. Promociones**
- ✅ CRUD completo
- ✅ Tipos: 2x1, porcentaje, monto fijo
- ✅ Filtrado automático por fechas de vigencia
- ✅ Relación con locales

**5. Locales (Venues)**
- ✅ CRUD completo
- ✅ Geolocalización con PostGIS
- ✅ Búsqueda por proximidad (radio)
- ✅ Validación de coordenadas (región Puno)

**6. Sistema Social**
- ✅ Reviews polimórficas (cualquier entidad)
- ✅ Sistema de favoritos polimórfico
- ✅ Ratings (1-5 estrellas)

### ✅ Panel de Administración

#### Panel Admin - Candelaria
- ✅ Gestión completa de danzas (CRUD)
- ✅ Gestión de galería histórica (CRUD)
- ✅ Subida de imágenes locales
- ✅ Vista previa de imágenes
- ✅ Buscador de recursos integrado
- ✅ Sistema de orden y activación
- ✅ Marcado de danzas destacadas

#### Panel Admin - Gestión de Socios
- ✅ Listado completo de socios
- ✅ Creación de nuevos socios
- ✅ Edición de información de socios
- ✅ Activación/desactivación de cuentas
- ✅ Panel de supervisión de contenido
- ✅ Estadísticas por socio
- ✅ Filtrado y búsqueda

#### Panel Admin - Usuarios
- ✅ Gestión completa de usuarios
- ✅ Cambio de roles
- ✅ Activación/desactivación

### ✅ Dashboard de Usuario

#### Dashboard General
- ✅ Página de bienvenida personalizada por rol
- ✅ Estadísticas dinámicas (locales, eventos, experiencias, promociones)
- ✅ Acciones rápidas contextuales
- ✅ Contenido reciente del usuario
- ✅ Filtrado de contenido por tipo
- ✅ Diferenciación admin vs socio (estadísticas globales vs personales)

#### Dashboard de Socio
- ✅ Gestión de locales propios
- ✅ Gestión de eventos propios
- ✅ Gestión de experiencias propias
- ✅ Gestión de promociones propias
- ✅ Formularios de creación con validación
- ✅ Formularios de edición

### ✅ Frontend

#### Páginas Públicas
- ✅ Página de inicio (landing page)
- ✅ Navegación principal
- ✅ Página de login/registro
- ✅ Página de búsqueda general
- ✅ Vista de Candelaria (danzas + galería)

#### Sistema de Diseño
- ✅ Paleta de colores oficial de marca
- ✅ Componentes reutilizables (cards, buttons, badges)
- ✅ Sistema de grids responsive
- ✅ Animaciones y transiciones
- ✅ Iconografía consistente (Lucide)
- ✅ Formularios estilizados

### ✅ Base de Datos

#### Migraciones
- ✅ Habilitación automática de PostGIS
- ✅ Tabla users (con roles)
- ✅ Tabla locales (con geometry)
- ✅ Tabla candelaria_danzas
- ✅ Tabla candelaria_galleries
- ✅ Tabla experiencias
- ✅ Tabla eventos (con time-sensitive filtering)
- ✅ Tabla promociones (con time-sensitive filtering)
- ✅ Tabla reviews (polimórfica)
- ✅ Tabla favorites (polimórfica)

#### Seeders
- ✅ Usuarios de prueba (tourist, socio, admin)
- ✅ Locales con coordenadas reales de Puno
- ✅ Eventos con fechas variadas
- ✅ Promociones activas
- ✅ Experiencias turísticas
- ✅ Danzas de la Candelaria
- ✅ Galería histórica

---

## 4. Módulos del Sistema

### 🎭 Módulo Candelaria

**Descripción:** Gestión completa de la Festividad de la Virgen de la Candelaria.

#### Componentes

**Backend:**
- `App\Http\Controllers\Admin\CandelariaDanzaController`
- `App\Http\Controllers\Admin\CandelariaGalleryController`
- `App\Http\Controllers\Admin\ResourceSearchController`
- `App\Models\CandelariaDanza`
- `App\Models\CandelariaGallery`

**Frontend:**
- `resources/views/admin/candelaria/danzas/`
  - `index.blade.php` - Listado de danzas
  - `create.blade.php` - Formulario de creación
  - `edit.blade.php` - Formulario de edición
- `resources/views/admin/candelaria/gallery/`
  - `index.blade.php` - Galería histórica
  - `create.blade.php` - Subir nueva foto
  - `edit.blade.php` - Editar foto

**Base de Datos:**
```sql
candelaria_danzas
├── id (PK)
├── name (string)
├── type (enum: mestiza, autoctona)
├── region (string, nullable)
├── description (text)
├── history (text, nullable)
├── characteristics (text, nullable)
├── image_url (string, nullable)
├── video_url (string, nullable)
├── is_featured (boolean)
├── is_active (boolean)
├── order (integer)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at

candelaria_galleries
├── id (PK)
├── title (string)
├── description (text, nullable)
├── image_url (string)
├── year (integer)
├── is_active (boolean)
├── order (integer)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at
```

**Funcionalidades Clave:**
- Gestión de danzas (mestizas y autóctonas)
- Subida de imágenes locales
- Buscador de recursos (Wikipedia + imágenes)
- Sistema de orden personalizado
- Marcado de danzas destacadas
- Galería histórica con años

**Rutas:**
```
GET  /admin/candelaria/danzas
POST /admin/candelaria/danzas
GET  /admin/candelaria/danzas/create
GET  /admin/candelaria/danzas/{id}/edit
PUT  /admin/candelaria/danzas/{id}
DELETE /admin/candelaria/danzas/{id}

GET  /admin/candelaria/gallery
POST /admin/candelaria/gallery
GET  /admin/candelaria/gallery/create
GET  /admin/candelaria/gallery/{id}/edit
PUT  /admin/candelaria/gallery/{id}
DELETE /admin/candelaria/gallery/{id}

GET  /admin/candelaria/resources/search
GET  /admin/candelaria/resources/search-images
GET  /admin/candelaria/resources/search-wikipedia
```

---

### 👥 Módulo Gestión de Socios

**Descripción:** Sistema completo de administración de cuentas de socios (partners).

#### Componentes

**Backend:**
- `App\Http\Controllers\Admin\SocioController`
- `App\Models\User` (role: 'socio')

**Frontend:**
- `resources/views/admin/socios/`
  - `index.blade.php` - Listado de socios
  - `create.blade.php` - Crear nuevo socio
  - `edit.blade.php` - Editar socio
  - `show.blade.php` - Ver detalles del socio
  - `supervision.blade.php` - Panel de supervisión

**Funcionalidades:**
- ✅ Listado completo de socios
- ✅ Creación de nuevas cuentas de socio
- ✅ Edición de información (nombre, email, teléfono, negocio)
- ✅ Activación/desactivación de cuentas
- ✅ Supervisión de contenido creado por socios
- ✅ Estadísticas por socio (locales, eventos, experiencias, promociones)
- ✅ Búsqueda y filtrado

**Rutas:**
```
GET    /admin/socios                     - Listado
POST   /admin/socios                     - Crear
GET    /admin/socios/create              - Formulario de creación
GET    /admin/socios/supervision         - Panel de supervisión
GET    /admin/socios/{id}                - Ver detalles
GET    /admin/socios/{id}/edit           - Formulario de edición
PUT    /admin/socios/{id}                - Actualizar
DELETE /admin/socios/{id}                - Eliminar
POST   /admin/socios/{id}/toggle-status  - Activar/desactivar
```

**Validaciones:**
- Email único
- Teléfono (10 dígitos mínimo)
- Nombre de negocio requerido
- Role automático 'socio'

---

### 🏢 Módulo Locales (Venues)

**Descripción:** Gestión de establecimientos turísticos con geolocalización.

#### Componentes

**Backend:**
- `App\Http\Controllers\Api\LocaleController`
- `App\Models\Locale`

**Base de Datos:**
```sql
locales
├── id (PK)
├── name (string)
├── description (text)
├── category (enum: restaurant, hotel, attraction, etc.)
├── address (string)
├── location (geometry - PostGIS POINT)
├── latitude (decimal)
├── longitude (decimal)
├── phone (string, nullable)
├── email (string, nullable)
├── website (string, nullable)
├── image_url (string, nullable)
├── is_active (boolean)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at
```

**Funcionalidades:**
- Geolocalización con PostGIS
- Búsqueda por proximidad (radio en metros)
- Validación de coordenadas (región Puno: -16.5 a -14.5 lat, -71 a -69 lng)
- Cálculo automático de distancias
- Relación con promociones

**API Endpoints:**
```
GET  /api/locales
POST /api/locales
GET  /api/locales/{id}
PUT  /api/locales/{id}
DELETE /api/locales/{id}

# Con geolocalización:
GET /api/locales?lat=-15.8402&lng=-70.0219&radius=5000
```

---

### 🧭 Módulo Experiencias

**Descripción:** Gestión de tours, actividades y paquetes turísticos.

#### Base de Datos
```sql
experiencias
├── id (PK)
├── name (string)
├── description (text)
├── difficulty (enum: easy, medium, hard)
├── duration (integer - minutos)
├── price (decimal)
├── max_people (integer)
├── includes (text)
├── requirements (text)
├── image_url (string, nullable)
├── locale_id (FK → locales, nullable)
├── is_active (boolean)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at
```

**Filtros Disponibles:**
- Por dificultad (easy, medium, hard)
- Por rango de precio
- Por duración
- Por ubicación (si tiene locale_id)

---

### 📅 Módulo Eventos

**Descripción:** Gestión de eventos con filtrado automático por fechas.

#### Base de Datos
```sql
eventos
├── id (PK)
├── title (string)
├── description (text)
├── category (enum: concert, festival, cultural, sports)
├── start_time (datetime)
├── end_time (datetime)
├── location (string)
├── latitude (decimal, nullable)
├── longitude (decimal, nullable)
├── price (decimal, nullable)
├── capacity (integer, nullable)
├── image_url (string, nullable)
├── is_active (boolean)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at
```

**Funcionalidades Especiales:**
- **Filtrado automático:** Los eventos expirados (`end_time < now()`) se ocultan automáticamente usando el scope `active()`
- Búsqueda por fecha
- Búsqueda por categoría
- Geolocalización opcional

---

### 🎟️ Módulo Promociones

**Descripción:** Gestión de ofertas y descuentos con vigencia temporal.

#### Base de Datos
```sql
promociones
├── id (PK)
├── title (string)
├── description (text)
├── type (enum: 2x1, percentage, fixed_amount)
├── discount_value (decimal)
├── start_date (date)
├── end_date (date)
├── terms (text, nullable)
├── image_url (string, nullable)
├── locale_id (FK → locales, nullable)
├── is_active (boolean)
├── user_id (FK → users)
├── created_at, updated_at, deleted_at
```

**Tipos de Descuento:**
- **2x1:** Compra uno, lleva dos
- **percentage:** Descuento porcentual (ej: 20% off)
- **fixed_amount:** Descuento de monto fijo (ej: S/. 50 off)

**Filtrado Automático:**
```php
// Solo promociones vigentes
Promocion::active()->get();

// Condiciones:
// - is_active = true
// - start_date <= now()
// - end_date >= now()
```

---

### 💬 Módulo Reviews

**Descripción:** Sistema de reseñas polimórfico para cualquier entidad.

#### Base de Datos
```sql
reviews
├── id (PK)
├── user_id (FK → users)
├── reviewable_type (string - model class)
├── reviewable_id (integer)
├── rating (integer 1-5)
├── title (string, nullable)
├── comment (text, nullable)
├── created_at, updated_at
```

**Uso:**
```php
// Cualquier modelo puede recibir reviews
$locale->reviews()->create([...]);
$evento->reviews()->create([...]);
$experiencia->reviews()->create([...]);
```

---

### ⭐ Módulo Favorites

**Descripción:** Sistema de favoritos polimórfico.

#### Base de Datos
```sql
favorites
├── id (PK)
├── user_id (FK → users)
├── favoriteable_type (string)
├── favoriteable_id (integer)
├── created_at, updated_at
```

---

## 5. Panel de Administración

### 🎛️ Estructura del Panel Admin

```
/admin
├── /candelaria
│   ├── /danzas          - Gestión de danzas
│   ├── /gallery         - Galería histórica
│   └── /resources       - Buscador de recursos
├── /socios              - Gestión de socios
│   └── /supervision     - Supervisión de contenido
└── /users               - Gestión de usuarios
```

### 🔐 Acceso al Panel Admin

**Middleware:** `role:admin`

Solo usuarios con rol `admin` pueden acceder a estas rutas.

**Ruta de acceso:** `/admin/*`

### 📊 Funcionalidades del Panel

#### Panel de Candelaria

**1. Gestión de Danzas**
- Crear, editar, eliminar danzas
- Subida de imágenes locales (JPEG, PNG, GIF, WEBP - máx 5MB)
- Clasificación por tipo (mestiza/autóctona)
- Marcado de danzas destacadas
- Sistema de orden personalizado
- Activación/desactivación
- Preview de imágenes antes de guardar
- Textareas auto-expandibles

**2. Galería Histórica**
- Subida de fotografías históricas
- Organización por año
- Títulos y descripciones
- Sistema de orden
- Activación/desactivación

**3. Buscador de Recursos**
- Búsqueda de imágenes en APIs externas
- Búsqueda de información en Wikipedia
- Integración directa desde los formularios

#### Panel de Socios

**Vista Principal (`/admin/socios`)**
- Tabla completa de socios
- Información: nombre, email, negocio, teléfono, estado
- Acciones: Ver, Editar, Activar/Desactivar
- Botón "Crear Nuevo Socio"

**Crear Socio (`/admin/socios/create`)**
- Formulario con validación:
  - Nombre completo
  - Email (único)
  - Contraseña (opcional en creación)
  - Teléfono
  - Nombre del negocio
  - Estado (activo/inactivo)

**Editar Socio (`/admin/socios/{id}/edit`)**
- Mismos campos que creación
- Password opcional (solo si se quiere cambiar)

**Ver Detalles (`/admin/socios/{id}`)**
- Información completa del socio
- Estadísticas de contenido:
  - Número de locales
  - Número de eventos
  - Número de experiencias
  - Número de promociones
- Fecha de registro
- Último acceso

**Panel de Supervisión (`/admin/socios/supervision`)**
- Vista consolidada de todo el contenido creado por socios
- Filtros por tipo de contenido
- Filtros por socio
- Acciones: Aprobar, Editar, Eliminar

---

## 6. Sistema de Autenticación y Roles

### 👤 Roles del Sistema

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **tourist** | Usuario turista | Ver contenido, crear reviews, favoritos |
| **socio** | Socio comercial | Todo lo de tourist + CRUD de su propio contenido |
| **admin** | Administrador | Acceso total, gestión de usuarios y socios, panel admin |

### 🔒 Middleware de Roles

**Archivo:** `app/Http/Middleware/RoleMiddleware.php`

**Uso en rutas:**
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Solo admins
});

Route::middleware(['auth:sanctum', 'role:socio,admin'])->group(function () {
    // Socios y admins
});
```

### 🎫 Flujo de Autenticación

**1. Registro**
```
Usuario → POST /api/register
↓
Validación de datos
↓
Hash de password (bcrypt)
↓
Creación de usuario con rol
↓
Return: user + token
```

**2. Login**
```
Usuario → POST /api/login
↓
Validación de credenciales
↓
Verificación de password
↓
Generación de token Sanctum
↓
Return: user + token
```

**3. Google OAuth**
```
Usuario → GET /api/auth/google
↓
Redirect a Google
↓
Usuario autoriza
↓
Google → Callback con código
↓
Obtener datos del usuario
↓
Crear o actualizar usuario
↓
Return: user + token
```

### 🛡️ Autorización en Controllers

**Patrón usado:**

```php
public function update(Request $request, $id)
{
    $item = Model::findOrFail($id);

    // Verificar propiedad si no es admin
    if ($request->user()->role !== 'admin'
        && $item->user_id !== $request->user()->id) {
        return response()->json([
            'success' => false,
            'message' => 'No autorizado'
        ], 403);
    }

    // Actualizar...
}
```

**Resumen:**
- **Admins:** Pueden editar/eliminar todo
- **Socios:** Solo su propio contenido
- **Tourists:** Sin permisos de edición

---

## 7. Base de Datos

### 🗄️ Diagrama de Relaciones

```
users (PK: id)
├─┬─ locales (FK: user_id)
│ ├─── experiencias (FK: locale_id)
│ └─── promociones (FK: locale_id)
├─── eventos (FK: user_id)
├─── candelaria_danzas (FK: user_id)
├─── candelaria_galleries (FK: user_id)
├─── reviews (FK: user_id)
└─── favorites (FK: user_id)
```

### 📊 Tablas Principales

#### users
```sql
id, name, email, password, role,
google_id, avatar, phone, bio,
is_active, email_verified_at,
created_at, updated_at
```

#### locales (con PostGIS)
```sql
id, name, description, category,
address, location (GEOMETRY),
latitude, longitude,
phone, email, website, image_url,
is_active, user_id,
created_at, updated_at, deleted_at
```

#### candelaria_danzas
```sql
id, name, type, region,
description, history, characteristics,
image_url, video_url,
is_featured, is_active, order,
user_id,
created_at, updated_at, deleted_at
```

#### candelaria_galleries
```sql
id, title, description,
image_url, year,
is_active, order, user_id,
created_at, updated_at, deleted_at
```

#### experiencias
```sql
id, name, description,
difficulty, duration, price,
max_people, includes, requirements,
image_url, locale_id, is_active,
user_id,
created_at, updated_at, deleted_at
```

#### eventos
```sql
id, title, description, category,
start_time, end_time,
location, latitude, longitude,
price, capacity, image_url,
is_active, user_id,
created_at, updated_at, deleted_at
```

#### promociones
```sql
id, title, description,
type, discount_value,
start_date, end_date, terms,
image_url, locale_id,
is_active, user_id,
created_at, updated_at, deleted_at
```

#### reviews (polimórfica)
```sql
id, user_id,
reviewable_type, reviewable_id,
rating, title, comment,
created_at, updated_at
```

#### favorites (polimórfica)
```sql
id, user_id,
favoriteable_type, favoriteable_id,
created_at, updated_at
```

### 🔍 Índices y Optimizaciones

**Índices Espaciales (PostGIS):**
```sql
CREATE INDEX locales_location_idx
ON locales USING GIST (location);
```

**Índices de Búsqueda:**
- `users.email` (UNIQUE)
- `locales.category`
- `eventos.category`
- `eventos.start_time`, `eventos.end_time`
- `promociones.start_date`, `promociones.end_date`

**Soft Deletes:**
- Todas las tablas principales tienen `deleted_at`
- Los registros eliminados se mantienen para historial

---

## 8. Frontend y Diseño

### 🎨 Sistema de Diseño

**Documento:** `PALETA_COLORES.md`

#### Paleta de Colores Oficial

| Color | Hex | RGB | Uso |
|-------|-----|-----|-----|
| **Primary** (Azul/Morado) | `#5930E8` | `89, 48, 232` | Botones principales, CTAs |
| **Secondary** (Verde Menta) | `#74FA8A` | `116, 250, 138` | Explorar, éxito |
| **Accent** (Amarillo/Dorado) | `#F2B914` | `242, 185, 20` | Promociones, ofertas |
| **Info** (Celeste) | `#B6DFE7` | `182, 223, 231` | Badges informativos |
| **Gray** (Gris Azulado) | `#B0C5C3` | `176, 197, 195` | Neutro, desactivado |

#### Variables CSS

**Archivo:** `resources/css/app.css`

```css
@theme {
  --color-primary: #5930E8;
  --color-secondary: #74FA8A;
  --color-accent: #F2B914;
  --color-info: #B6DFE7;
  --color-gray: #B0C5C3;
  --color-success: #74FA8A;
  --color-warning: #F2B914;
  --color-danger: #E74C3C;
}
```

### 🧩 Componentes Reutilizables

#### Botones
```html
<button class="btn btn-primary">Acción Principal</button>
<button class="btn btn-secondary">Acción Secundaria</button>
<button class="btn btn-outline">Acción Outline</button>
```

#### Cards
```html
<div class="card">
  <img src="..." class="card-image">
  <div class="p-4">
    <h3>Título</h3>
    <p>Contenido...</p>
  </div>
</div>
```

#### Badges
```html
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Activo</span>
<span class="badge badge-warning">Promoción</span>
```

#### Grids
```html
<div class="grid grid-2">...</div>  <!-- 2 columnas -->
<div class="grid grid-3">...</div>  <!-- 3 columnas -->
<div class="grid grid-4">...</div>  <!-- 4 columnas -->
```

### 📱 Responsive Design

**Breakpoints de Tailwind:**
- `sm:` - 640px
- `md:` - 768px
- `lg:` - 1024px
- `xl:` - 1280px

**Patrón Mobile-First:**
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
  <!-- 1 columna mobile, 2 tablet, 3 desktop -->
</div>
```

### 🗺️ Mapas Interactivos

**Librería:** Leaflet + OpenStreetMap

**Implementación:**
```javascript
const map = L.map('map').setView([-15.8402, -70.0219], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Marcador arrastrable
const marker = L.marker([-15.8402, -70.0219], {draggable: true}).addTo(map);
```

### 📐 Layout Principal

**Archivo:** `resources/views/layouts/app.blade.php`

**Estructura:**
```
┌─────────────────────────────┐
│         Navbar              │
├─────────────────────────────┤
│                             │
│         @yield('content')   │
│                             │
├─────────────────────────────┤
│         Footer              │
└─────────────────────────────┘
```

**Dashboard Layout:** `resources/views/layouts/dashboard.blade.php`

**Estructura:**
```
┌────────┬────────────────────┐
│        │                    │
│ Side   │                    │
│ bar    │  @yield('content') │
│        │                    │
│        │                    │
└────────┴────────────────────┘
```

---

## 9. Configuración y Deployment

### ⚙️ Variables de Entorno (.env)

```env
# Aplicación
APP_NAME=Turiscovery
APP_ENV=local|production
APP_KEY=base64:...
APP_DEBUG=true|false
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=turiscovery
DB_USERNAME=postgres
DB_PASSWORD=tu_password

# Google OAuth
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:8000

# Mail (opcional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
```

### 🚀 Deployment a Producción

#### Requisitos del Servidor

- **SO:** Ubuntu 20.04+ / Debian 11+
- **PHP:** 8.2+
- **PostgreSQL:** 14+ con PostGIS
- **Node.js:** 18+
- **Nginx:** 1.18+ o Apache 2.4+
- **SSL:** Certificado Let's Encrypt

#### Pasos de Deployment

**1. Configurar Servidor**
```bash
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-pgsql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-gd
sudo apt install postgresql-14 postgresql-14-postgis-3
sudo apt install nginx
```

**2. Clonar Proyecto**
```bash
cd /var/www
git clone https://github.com/tu-repo/turiscovery.git
cd turiscovery
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

**3. Configurar .env**
```bash
cp .env.example .env
php artisan key:generate
# Editar .env con credenciales de producción
```

**4. Configurar Base de Datos**
```bash
sudo -u postgres psql
CREATE DATABASE turiscovery;
\q

php artisan migrate --force
php artisan db:seed --force
```

**5. Configurar Permisos**
```bash
sudo chown -R www-data:www-data /var/www/turiscovery
sudo chmod -R 755 /var/www/turiscovery
sudo chmod -R 775 /var/www/turiscovery/storage
sudo chmod -R 775 /var/www/turiscovery/bootstrap/cache
```

**6. Configurar Nginx**
```nginx
server {
    listen 80;
    server_name turiscovery.com;
    root /var/www/turiscovery/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}
```

**7. SSL con Let's Encrypt**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d turiscovery.com
```

**8. Optimizaciones**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**9. Queue Worker (si se usa)**
```bash
# Crear servicio systemd
sudo nano /etc/systemd/system/turiscovery-worker.service
```

```ini
[Unit]
Description=Turiscovery Queue Worker

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/turiscovery/artisan queue:work

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable turiscovery-worker
sudo systemctl start turiscovery-worker
```

---

## 10. Pendientes y Mejoras

### 🔴 Prioridad Alta (Crítico)

#### Testing
- ⬜ Tests unitarios para modelos
- ⬜ Tests de integración para API
- ⬜ Tests de autorización (RBAC)
- ⬜ Tests de geolocalización

#### Seguridad
- ⬜ Implementar CSRF tokens en formularios web
- ⬜ Configurar CORS correctamente para producción
- ⬜ Rate limiting en endpoints públicos
- ⬜ Validación de archivos subidos (tipos MIME, virus scan)
- ⬜ Sanitización de inputs HTML (XSS prevention)

#### Performance
- ⬜ Implementar cache de consultas frecuentes (Redis)
- ⬜ Optimizar queries N+1 con eager loading
- ⬜ Implementar paginación en todas las listas
- ⬜ Comprimir assets (Gzip, Brotli)

### 🟡 Prioridad Media (Importante)

#### Frontend Público
- ⬜ Página de detalle de danzas
- ⬜ Página de detalle de eventos
- ⬜ Página de detalle de experiencias
- ⬜ Página de detalle de promociones
- ⬜ Página de perfil de socio/local
- ⬜ Sistema de búsqueda avanzada
- ⬜ Filtros por categoría, precio, ubicación
- ⬜ Mapa interactivo general

#### Dashboard de Socio
- ⬜ Analytics avanzado:
  - Vistas por contenido
  - Clicks en contacto
  - Reviews recibidas
  - Favoritos recibidos
- ⬜ Gráficos de estadísticas (Chart.js)
- ⬜ Exportación de datos (CSV, PDF)
- ⬜ Notificaciones de nuevas reviews

#### Panel Admin
- ⬜ Dashboard principal con métricas globales
- ⬜ Gestión de categorías
- ⬜ Gestión de tags
- ⬜ Moderación de reviews
- ⬜ Logs de actividad
- ⬜ Respaldo y restauración de datos

#### Funcionalidades Nuevas
- ⬜ Sistema de mensajería (socio ↔ turista)
- ⬜ Sistema de reservas
- ⬜ Calendario de eventos
- ⬜ Notificaciones push
- ⬜ Integración con redes sociales (compartir)
- ⬜ Multiidioma (español, inglés, quechua)

### 🟢 Prioridad Baja (Mejoras)

#### UX/UI
- ⬜ Modo oscuro
- ⬜ Animaciones más fluidas
- ⬜ Skeleton loaders
- ⬜ Infinite scroll en listados
- ⬜ Mejorar accesibilidad (ARIA labels)

#### SEO
- ⬜ Meta tags dinámicos por página
- ⬜ Open Graph para redes sociales
- ⬜ Sitemap.xml generado automáticamente
- ⬜ robots.txt configurado
- ⬜ URLs amigables (slugs)

#### DevOps
- ⬜ CI/CD con GitHub Actions
- ⬜ Docker containers
- ⬜ Backups automáticos de BD
- ⬜ Monitoreo de errores (Sentry)
- ⬜ Logs centralizados
- ⬜ Staging environment

#### Documentación
- ⬜ API documentation (Swagger/OpenAPI)
- ⬜ Guía de contribución
- ⬜ Video tutoriales
- ⬜ Changelog detallado

---

## 11. Guía de Mantenimiento

### 🔧 Tareas de Mantenimiento Regulares

#### Diarias
- Revisar logs de errores: `storage/logs/laravel.log`
- Verificar estado de queue workers
- Monitorear uso de disco

#### Semanales
- Backup de base de datos
- Limpiar archivos temporales
- Actualizar dependencias de seguridad
- Revisar reviews reportadas

#### Mensuales
- Analizar métricas de uso
- Optimizar índices de BD
- Actualizar dependencias menores
- Auditoría de seguridad

### 📦 Actualización de Dependencias

**Backend (Composer):**
```bash
# Ver dependencias desactualizadas
composer outdated

# Actualizar todo (cuidado en producción)
composer update

# Actualizar solo paquete específico
composer update vendor/package
```

**Frontend (npm):**
```bash
# Ver dependencias desactualizadas
npm outdated

# Actualizar todo
npm update

# Actualizar específico
npm update package-name
```

### 🗄️ Backup de Base de Datos

**Script de Backup:**
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/turiscovery"
DB_NAME="turiscovery"

mkdir -p $BACKUP_DIR

# Backup completo
pg_dump -U postgres $DB_NAME > $BACKUP_DIR/turiscovery_$DATE.sql

# Comprimir
gzip $BACKUP_DIR/turiscovery_$DATE.sql

# Mantener solo últimos 30 días
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completado: turiscovery_$DATE.sql.gz"
```

**Restaurar Backup:**
```bash
gunzip -c backup.sql.gz | psql -U postgres turiscovery
```

### 🧹 Limpieza de Archivos

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Limpiar storage
php artisan storage:link  # Recrear symlink
rm -rf storage/logs/*.log  # Logs antiguos

# Optimizar autoloader
composer dump-autoload --optimize
```

### 📊 Monitoreo de Performance

**Comandos útiles:**
```bash
# Ver procesos PHP
ps aux | grep php

# Ver uso de memoria
free -h

# Ver uso de disco
df -h

# Ver conexiones a PostgreSQL
sudo -u postgres psql -c "SELECT * FROM pg_stat_activity;"

# Ver queries lentas (configurar en postgresql.conf)
sudo -u postgres psql -c "SELECT * FROM pg_stat_statements ORDER BY total_time DESC LIMIT 10;"
```

---

## 12. Solución de Problemas

### ❗ Problemas Comunes

#### Error: "could not find driver"
**Problema:** Extensiones PostgreSQL no habilitadas en PHP

**Solución:**
```bash
# Editar php.ini
sudo nano /etc/php/8.2/fpm/php.ini

# Descomentar:
extension=pdo_pgsql
extension=pgsql

# Reiniciar PHP
sudo systemctl restart php8.2-fpm
```

#### Error: "PostGIS extension not found"
**Problema:** PostGIS no instalado

**Solución:**
```bash
# Ubuntu/Debian
sudo apt install postgresql-14-postgis-3

# Habilitar en la BD
sudo -u postgres psql turiscovery
CREATE EXTENSION IF NOT EXISTS postgis;
```

#### Error: "Permission denied" en storage/
**Problema:** Permisos incorrectos

**Solución:**
```bash
sudo chown -R www-data:www-data storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

#### Error: "419 Page Expired" en formularios
**Problema:** Token CSRF expirado

**Solución:**
```blade
<!-- Agregar en formularios -->
@csrf

<!-- O en JavaScript -->
axios.defaults.headers.common['X-CSRF-TOKEN'] =
  document.querySelector('meta[name="csrf-token"]').content;
```

#### Error: "Too many requests" al hacer login
**Problema:** Rate limiting activado

**Solución:**
```php
// En app/Http/Controllers/Auth/LoginController.php
protected function attemptLogin(Request $request)
{
    // Aumentar intentos permitidos
    return $this->guard()->attempt(
        $this->credentials($request),
        $request->filled('remember')
    );
}
```

#### Imágenes no se cargan
**Problema:** Symlink de storage no creado

**Solución:**
```bash
php artisan storage:link
```

#### Queries lentas
**Problema:** Falta de índices o queries N+1

**Solución:**
```php
// Usar eager loading
$locales = Locale::with('promociones', 'experiencias')->get();

// Agregar índices en migraciones
$table->index('category');
$table->index('is_active');
```

---

## 📞 Contacto y Soporte

### 📚 Recursos de Documentación

- **Laravel:** https://laravel.com/docs
- **PostgreSQL:** https://www.postgresql.org/docs/
- **PostGIS:** https://postgis.net/docs/
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Leaflet:** https://leafletjs.com/reference.html

### 🐛 Reporte de Bugs

Para reportar bugs o solicitar funcionalidades:

1. Revisar issues existentes en GitHub
2. Crear nuevo issue con:
   - Descripción clara del problema
   - Pasos para reproducir
   - Resultado esperado vs actual
   - Screenshots si aplica
   - Versión de Laravel y PHP

---

## 📝 Notas Finales

### ✅ Resumen de Estado

**Lo que funciona:**
- ✅ Backend API completo
- ✅ Autenticación y RBAC
- ✅ Panel admin de Candelaria
- ✅ Panel admin de Socios
- ✅ Dashboard de usuario
- ✅ Geolocalización con PostGIS
- ✅ Sistema de diseño con paleta de marca

**Lo que falta:**
- ⬜ Frontend público completo
- ⬜ Analytics avanzado
- ⬜ Testing comprehensivo
- ⬜ Deployment a producción

### 🎯 Próximos Pasos Recomendados

1. **Completar Frontend Público** (2-3 semanas)
   - Páginas de detalle
   - Sistema de búsqueda
   - Mapa interactivo

2. **Implementar Testing** (1 semana)
   - Tests unitarios
   - Tests de integración
   - Tests E2E

3. **Preparar para Producción** (1 semana)
   - Configurar servidor
   - SSL/HTTPS
   - Backups automáticos
   - Monitoreo

4. **Analytics y Optimización** (1-2 semanas)
   - Dashboard de analytics
   - Optimización de queries
   - Cache

### 📖 Changelog

**v2.0 (2026-02-09)**
- ✅ Sistema de diseño unificado con paleta oficial
- ✅ Panel admin de Candelaria completo
- ✅ Panel admin de Socios completo
- ✅ Dashboard con estadísticas dinámicas
- ✅ Subida de imágenes optimizada
- ✅ Documentación completa del sistema

**v1.0 (2026-01-15)**
- ✅ Implementación inicial
- ✅ API de los 4 pilares
- ✅ Autenticación y roles
- ✅ Geolocalización con PostGIS

---

**Documento creado por:** Sistema Turiscovery
**Última revisión:** 2026-02-09
**Mantenido por:** Equipo de Desarrollo

---

🏔️ **Turiscovery** - Descubre Puno, Vive la Experiencia
