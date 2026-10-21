# ✅ Lista de Tareas Pendientes - Turiscovery

**Última actualización:** 2026-10-19 (migración a React + Three.js)

---

## 🔴 URGENTE - Antes de Producción

### Seguridad
- [x] Implementar CSRF tokens en todos los formularios web (Sanctum: cookie XSRF-TOKEN en cada petición de la SPA)
- [x] Configurar CORS para producción (`config/cors.php`)
- [x] Agregar rate limiting en endpoints públicos
- [x] Implementar validación de tipos MIME en uploads
- [x] Sanitización de HTML en contenido de usuario (XSS): React escapa todo el texto y no hay `dangerouslySetInnerHTML`
- [ ] Configurar HTTPS/SSL en producción
- [x] Cambiar todas las contraseñas por defecto

### Testing
- [ ] Tests unitarios de modelos (User, Locale, etc.)
- [x] Tests de API endpoints (GET, POST, PUT, DELETE)
- [x] Tests de autorización (verificar RBAC)
- [x] Tests de geolocalización (PostGIS)
- [ ] Tests de validación de formularios

### Performance
- [ ] Implementar paginación en:
  - [x] `/api/locales`
  - [x] `/api/eventos`
  - [x] `/api/experiencias`
  - [x] `/api/promociones`
  - [ ] `/admin/socios`
  - [ ] `/admin/candelaria/danzas`
- [ ] Optimizar queries con `eager loading`:
  - [ ] `Locale::with('promociones', 'experiencias')`
  - [ ] `Evento::with('user')`
  - [ ] Revisar queries N+1 con `debugbar`
- [ ] Implementar cache de consultas frecuentes (Redis)
- [x] Comprimir imágenes automáticamente al subir

---

## 🟡 IMPORTANTE - Frontend Público

### Páginas de Detalle
- [x] `/candelaria/danzas/{id}` - Detalle de danza
  - [x] Mostrar historia completa
  - [x] Características detalladas
  - [x] Video embebido si existe
  - [x] Botón compartir en redes sociales

- [x] `/eventos/{id}` - Detalle de evento
  - [x] Mostrar mapa de ubicación
  - [x] Contador de días restantes
  - [x] Botón "Agregar a calendario"
  - [x] Reviews y ratings

- [x] `/experiencias/{id}` - Detalle de experiencia
  - [x] Galería de imágenes
  - [x] Itinerario completo
  - [x] Botón "Reservar"
  - [x] Reviews y ratings

- [x] `/promociones/{id}` - Detalle de promoción
  - [x] Términos y condiciones
  - [x] Vigencia visible
  - [x] Información del local
  - [x] Botón "Usar promoción"

- [x] `/locales/{id}` - Perfil de local/socio
  - [x] Información completa
  - [x] Ubicación en mapa
  - [x] Eventos, experiencias, promociones del local
  - [x] Reviews y ratings
  - [ ] Formulario de contacto (hoy solo hay un enlace `mailto:` en /contacto)

### Sistema de Búsqueda
- [x] Página `/search` con filtros avanzados:
  - [x] Por tipo de contenido (locales, eventos, experiencias, promociones)
  - [x] Por categoría
  - [x] Por rango de precio
  - [ ] Por ubicación (mapa + radio)
  - [ ] Por fecha (eventos)
  - [x] Por dificultad (experiencias)

### Mapa Interactivo
- [x] Página `/mapa` con Leaflet
  - [x] Mostrar todos los locales como marcadores
  - [x] Filtros por categoría
  - [x] Popup con información básica
  - [x] Click para ver detalle

### Sistema Social
- [x] Formulario de reviews
  - [x] Rating con estrellas
  - [x] Título y comentario
  - [ ] Validación de contenido ofensivo

- [x] Botón de favoritos en todas las páginas de detalle
- [x] Página `/mis-favoritos` para usuarios

### Otros
- [x] Página `/sobre-nosotros`
- [x] Página `/contacto`
- [x] Página `/terminos-y-condiciones`
- [x] Página `/politica-de-privacidad`

---

## 🟢 MEJORAS - Dashboard y Admin

### Dashboard de Socio
- [ ] Panel de analytics:
  - [ ] Vistas por contenido (Chart.js)
  - [ ] Clicks en botones de contacto
  - [ ] Reviews recibidas (últimas 10)
  - [ ] Favoritos recibidos
  - [ ] Gráfico de vistas por mes

- [ ] Notificaciones:
  - [ ] Nuevas reviews
  - [ ] Promociones próximas a vencer
  - [ ] Eventos próximos

- [ ] Exportación de datos:
  - [ ] Exportar lista de locales (CSV)
  - [ ] Exportar estadísticas (PDF)

### Panel Admin - Dashboard Principal
- [x] Crear `/admin/dashboard` (es "Supervisión", en `/admin`):
  - [x] Total de usuarios (tourists, socios)
  - [x] Total de contenido (locales, eventos, etc.)
  - [ ] Gráfico de registros por mes
  - [ ] Contenido más popular
  - [ ] Reviews recientes
  - [ ] Actividad reciente

### Panel Admin - Gestión de Contenido
- [ ] Moderación de reviews:
  - [ ] Lista de reviews reportadas
  - [ ] Aprobar/rechazar/eliminar

- [ ] Logs de actividad:
  - [ ] Registro de acciones importantes
  - [ ] Filtrado por usuario/fecha

- [ ] Gestión de categorías:
  - [ ] CRUD de categorías personalizadas
  - [ ] Orden y activación

---

## 🔵 FUNCIONALIDADES NUEVAS

### Sistema de Reservas
- [x] Modelo `Booking` (reserva)
- [x] Estado: pendiente, confirmada, cancelada
- [x] Relación con experiencias
- [x] Panel de gestión de reservas para socios
- [x] Notificaciones por email (`BookingNotification`, si el usuario las tiene activadas; siempre llega el aviso en la app)

### Sistema de Mensajería
- [ ] Modelo `Message`
- [ ] Chat turista ↔ socio
- [ ] Notificaciones en tiempo real (Pusher o similar)
- [ ] Historial de conversaciones

### Calendario de Eventos
- [ ] Vista de calendario mensual
- [ ] Filtros por categoría
- [ ] Vista de lista/cuadrícula
- [ ] Sincronización con Google Calendar

### Multiidioma
- [ ] Español (completo)
- [ ] Inglés (traducción)
- [ ] Quechua (básico)
- [ ] Selector de idioma en navbar
- [ ] Usar `laravel-translatable`

### Integración Redes Sociales
- [ ] Botones de compartir (Facebook, Twitter, WhatsApp) — hoy hay un botón "Compartir" genérico (menú del sistema o copiar enlace)
- [ ] Meta tags Open Graph
- [ ] Twitter Cards
- [ ] Login con Facebook

---

## 🟣 DEPLOYMENT Y DEVOPS

### Servidor de Producción
- [ ] Configurar VPS (DigitalOcean, AWS, etc.)
- [ ] Instalar stack LEMP (Linux, Nginx, PHP, PostgreSQL)
- [ ] Instalar PostGIS
- [ ] Configurar firewall (UFW)
- [ ] Configurar swap si es necesario

### SSL/HTTPS
- [ ] Obtener certificado Let's Encrypt
- [ ] Configurar renovación automática
- [ ] Forzar HTTPS en Laravel (`AppServiceProvider`)

### CI/CD
- [x] GitHub Actions workflow (`.github/workflows/ci.yml`):
  - [x] Ejecutar tests automáticamente (Pest con PostGIS y build del frontend)
  - [ ] Deploy automático a staging
  - [ ] Deploy manual a producción

### Backups
- [ ] Script de backup de base de datos
- [ ] Cron job diario
- [ ] Almacenamiento en S3 o similar
- [ ] Backup de archivos storage/

### Monitoreo
- [ ] Instalar Sentry para tracking de errores
- [ ] Configurar logs centralizados
- [ ] Alertas por email/Slack en errores críticos
- [ ] Monitoreo de uptime (UptimeRobot)

### Optimizaciones Producción
- [ ] Ejecutar `php artisan config:cache`
- [ ] Ejecutar `php artisan route:cache`
- [ ] Ejecutar `php artisan view:cache`
- [ ] Configurar queue worker como servicio systemd
- [ ] Configurar OPcache en PHP

---

## 🟤 DOCUMENTACIÓN

### Documentación de API
- [ ] Implementar Swagger/OpenAPI
- [ ] Documentar todos los endpoints
- [ ] Ejemplos de requests/responses
- [ ] Códigos de error

### Guías de Usuario
- [ ] Manual para socios (cómo crear contenido)
- [ ] Manual para turistas (cómo usar la plataforma)
- [ ] Video tutoriales básicos

### Documentación Técnica
- [ ] Guía de contribución (CONTRIBUTING.md)
- [ ] Guía de estilo de código
- [ ] Arquitectura de decisiones (ADR)
- [ ] Changelog detallado

---

## 🟠 UX/UI

### Accesibilidad
- [ ] Agregar ARIA labels a elementos interactivos
- [ ] Asegurar contraste de colores WCAG AA
- [ ] Navegación por teclado completa
- [ ] Textos alternativos en imágenes

### Mejoras de Diseño
- [ ] Implementar modo oscuro
- [x] Skeleton loaders en lugar de spinners
- [ ] Transiciones más fluidas
- [ ] Animaciones de scroll (AOS.js)
- [ ] Infinite scroll en listados

### SEO
- [ ] Meta tags dinámicos por página
- [ ] Open Graph completo
- [ ] Sitemap.xml automático
- [ ] robots.txt configurado
- [ ] URLs amigables (slugs)
- [ ] Schema.org markup para eventos y locales

---

## 📊 Métricas de Progreso

### Backend
- [x] API completa (100%)
- [x] Autenticación (100%)
- [x] RBAC (100%)
- [x] Geolocalización (100%)
- [ ] Testing (40%)
- [ ] Performance (60%)

### Frontend
- [x] Layout base (100%)
- [x] Sistema de diseño (100%)
- [x] Dashboard (100%)
- [x] Panel admin (100%)
- [ ] Páginas públicas (30%)
- [ ] Sistema social (0%)

### DevOps
- [ ] CI/CD (0%)
- [ ] Deployment (0%)
- [ ] Monitoreo (0%)
- [ ] Backups (0%)

---

## 🎯 Sprint Planning Sugerido

### Sprint 1 (1-2 semanas) - Frontend Público
- [ ] Páginas de detalle (danzas, eventos, experiencias, promociones)
- [ ] Sistema de búsqueda básico
- [ ] Mapa interactivo

### Sprint 2 (1 semana) - Testing
- [ ] Tests unitarios
- [ ] Tests de API
- [ ] Tests de autorización

### Sprint 3 (1 semana) - Analytics
- [ ] Dashboard de socio con estadísticas
- [ ] Gráficos Chart.js
- [ ] Exportación de datos

### Sprint 4 (1 semana) - Deployment
- [ ] Configurar servidor
- [ ] SSL/HTTPS
- [ ] CI/CD
- [ ] Backups

### Sprint 5 (1 semana) - Optimizaciones
- [ ] Paginación
- [ ] Cache
- [ ] Optimización de queries
- [ ] Compresión de assets

---

## 📝 Notas

- Priorizar seguridad y testing antes de deployment
- Frontend público es esencial para usuarios finales
- Analytics es importante para retención de socios
- No olvidar documentación

---

**Mantener este documento actualizado con cada task completada** ✅
