# Ñan Puno - Guía de Inicio Rápido

## ¡Comenzar en 5 Minutos! 🚀

### Paso 1: Verificar Requisitos

```powershell
# Verificar PHP
php --version  # Debe ser 8.2+

# Verificar Composer
composer --version

# Verificar Node.js
node --version  # Debe ser 18+

# Verificar PostgreSQL
psql --version
```

### Paso 2: Configurar Base de Datos

```powershell
# Conectar a PostgreSQL
psql -U postgres

# Crear base de datos
CREATE DATABASE nanpuno;

# Salir
\q
```

### Paso 3: Configurar Variables de Entorno

Edita el archivo `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nanpuno
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña

# Opcional: Google OAuth (dejar vacío por ahora)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### Paso 4: Instalar y Ejecutar Migraciones

```powershell
# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Ejecutar migraciones (crea todas las tablas)
php artisan migrate

# Generar datos de prueba  
php artisan db:seed
```

### Paso 5: Iniciar la Aplicación

```powershell
# Opción 1: Usar el comando composer dev (recomendado)
composer dev

# Opción 2: Iniciar servicios por separado
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev
```

🎉 **¡Listo!** Abre tu navegador en http://localhost:8000

---

## Usuarios de Prueba

Después de ejecutar `php artisan db:seed`, tendrás estos usuarios:

| Email | Contraseña | Rol | Permisos |
|-------|------------|-----|----------|
| turista@example.com | password123 | Tourist | Ver contenido, crear reseñas, favoritos |
| socio@example.com | password123 | Socio | Todo lo de Tourist + CRUD de su contenido |
| admin@example.com | password123 | Admin | Acceso total |

---

## Probar la API

### 1. Login
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "turista@example.com",
  "password": "password123"
}

# Guarda el access_token de la respuesta
```

### 2. Ver Eventos Activos
```bash
GET http://localhost:8000/api/eventos
```

### 3. Ver Promociones (solo las activas)
```bash
GET http://localhost:8000/api/promociones
```

### 4. Buscar Locales Cercanos
```bash
GET http://localhost:8000/api/locales?lat=-15.8402&lng=-70.0219&radius=5000
```

### 5. Crear una Reseña (requiere autenticación)
```bash
POST http://localhost:8000/api/reviews
Authorization: Bearer TU_TOKEN_AQUI
Content-Type: application/json

{
  "reviewable_type": "App\\Models\\Locale",
  "reviewable_id": 1,
  "rating": 5,
  "title": "¡Excelente!",
  "comment": "Muy buena experiencia"
}
```

---

## Estructura del Proyecto

```
nanpuno/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/         # LoginController, RegisterController, GoogleAuthController
│   │   │   └── Api/          # Los 4 pilares + Reviews + Favorites
│   │   └── Middleware/       # RoleMiddleware (RBAC)
│   └── Models/               # User, Locale, Candelaria, Experiencia, Evento, Promocion, Review, Favorite
├── database/
│   ├── migrations/           # 9 migraciones (PostGIS + todas las tablas)
│   └── seeders/              # Datos de prueba
├── routes/
│   ├── api.php               # Todas las rutas de API
│   └── web.php               # Rutas del frontend
└── resources/
    ├── css/                  # Estilos modernos con Tailwind
    ├── js/                   # Cliente API JavaScript
    └── views/                # Vistas Blade
```

---

## Funcionalidades Implementadas ✅

### Backend
- ✅ **Autenticación**: Registro, Login, Google OAuth
- ✅ **RBAC**: Roles (Tourist, Socio, Admin)
- ✅ **API RESTful** para los 4 pilares
- ✅ **Geolocalización** con PostGIS
- ✅ **Filtrado automático** de contenido expirado
- ✅ **Reviews y Favorites**
- ✅ **Validación de inputs**
- ✅ **Rate limiting** en login

### Frontend
- ✅ **Página de inicio** con navegación a los 4 pilares
- ✅ **Cliente JavaScript** para consumir la API
- ✅ **Estilos modernos** con Tailwind CSS
- ✅ **Layout responsive**

### Base de Datos
- ✅ **PostGIS** para geolocalización
- ✅ **9 migraciones** completas
- ✅ **Soft deletes** para datos históricos
- ✅ **Seeder** con datos realistas

---

## Próximos Pasos

1. **Configurar Google OAuth** (opcional):
   - Ve a https://console.cloud.google.com
   - Crea credenciales OAuth 2.0
   - Agrega a `.env`: `GOOGLE_CLIENT_ID` y `GOOGLE_CLIENT_SECRET`

2. **Desarrollar Más Vistas Frontend**:
   - Páginas individuales para cada pilar
   - Formularios de creación de contenido
   - Panel de control para socios

3. **Agregar Mapa Interactivo**:
   - Integrar Leaflet.js
   - Drag & drop de marcadores para ubicaciones

4. **Testing**:
   - Escribir tests unitarios
   - Tests de integración para API

5. **Deployment**:
   - Configurar servidor de producción
   - HTTPS/SSL
   - Backups automáticos

---

## Comandos Útiles

```powershell
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas API
php artisan route:list --path=api

# Crear una nueva migración
php artisan make:migration create_something_table

# Crear un nuevo controlador
php artisan make:controller Api/SomethingController

# Compilar assets para producción
npm run build

# Ejecutar tests
php artisan test
```

---

## Solución de Problemas

### Error: "could not find driver"
```powershell
# Activar extensión pgsql en php.ini
extension=pdo_pgsql
extension=pgsql
```

### Error: "PostGIS extension not found"
```powershell
# Instalar PostGIS
# Windows: Usar Stack Builder
# Linux: sudo apt-get install postgis postgresql-XX-postgis-3
```

### Error de permisos en storage/
```powershell
# Windows (PowerShell como Admin)
icacls "storage" /grant Everyone:F /t
icacls "bootstrap/cache" /grant Everyone:F /t
```

---

## Soporte

- **Documentación Laravel**: https://laravel.com/docs
- **PostGIS**: https://postgis.net/docs
- **Tailwind CSS**: https://tailwindcss.com/docs

---

¡Disfruta desarrollando con Ñan Puno! 🏔️
