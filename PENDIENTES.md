# ✅ Lista de Tareas Pendientes - Turiscovery

**Última actualización:** 2026-02-09

---

## 🔴 URGENTE - Antes de Producción

### Seguridad
- [ ] Implementar CSRF tokens en todos los formularios web
- [ ] Configurar CORS para producción (`config/cors.php`)
- [ ] Agregar rate limiting en endpoints públicos
- [ ] Implementar validación de tipos MIME en uploads
- [ ] Sanitización de HTML en contenido de usuario (XSS)
- [ ] Configurar HTTPS/SSL en producción
- [ ] Cambiar todas las contraseñas por defecto

### Testing
- [ ] Tests unitarios de modelos (User, Locale, etc.)
- [ ] Tests de API endpoints (GET, POST, PUT, DELETE)
- [ ] Tests de autorización (verificar RBAC)
- [ ] Tests de geolocalización (PostGIS)
- [ ] Tests de validación de formularios

### Performance
- [ ] Implementar paginación en:
  - [ ] `/api/locales`
  - [ ] `/api/eventos`
  - [ ] `/api/experiencias`
  - [ ] `/api/promociones`
  - [ ] `/admin/socios`
  - [ ] `/admin/candelaria/danzas`
- [ ] Optimizar queries con `eager loading`:
  - [ ] `Locale::with('promociones', 'experiencias')`
  - [ ] `Evento::with('user')`
  - [ ] Revisar queries N+1 con `debugbar`
- [ ] Implementar cache de consultas frecuentes (Redis)
- [ ] Comprimir imágenes automáticamente al subir

---

## 🟡 IMPORTANTE - Frontend Público

### Páginas de Detalle
- [ ] `/candelaria/danzas/{id}` - Detalle de danza
  - [ ] Mostrar historia completa
  - [ ] Características detalladas
  - [ ] Video embebido si existe
  - [ ] Botón compartir en redes sociales

- [ ] `/eventos/{id}` - Detalle de evento
  - [ ] Mostrar mapa de ubicación
  - [ ] Contador de días restantes
  - [ ] Botón "Agregar a calendario"
  - [ ] Reviews y ratings

- [ ] `/experiencias/{id}` - Detalle de experiencia
  - [ ] Galería de imágenes
  - [ ] Itinerario completo
  - [ ] Botón "Reservar"
  - [ ] Reviews y ratings

- [ ] `/promociones/{id}` - Detalle de promoción
  - [ ] Términos y condiciones
  - [ ] Vigencia visible
  - [ ] Información del local
  - [ ] Botón "Usar promoción"

- [ ] `/locales/{id}` - Perfil de local/socio
  - [ ] Información completa
  - [ ] Ubicación en mapa
  - [ ] Eventos, experiencias, promociones del local
  - [ ] Reviews y ratings
  - [ ] Formulario de contacto

### Sistema de Búsqueda
- [ ] Página `/search` con filtros avanzados:
  - [ ] Por tipo de contenido (locales, eventos, experiencias, promociones)
  - [ ] Por categoría
  - [ ] Por rango de precio
  - [ ] Por ubicación (mapa + radio)
  - [ ] Por fecha (eventos)
  - [ ] Por dificultad (experiencias)

### Mapa Interactivo
- [ ] Página `/mapa` con Leaflet
  - [ ] Mostrar todos los locales como marcadores
  - [ ] Filtros por categoría
  - [ ] Popup con información básica
  - [ ] Click para ver detalle

### Sistema Social
- [ ] Formulario de reviews
  - [ ] Rating con estrellas
  - [ ] Título y comentario
  - [ ] Validación de contenido ofensivo

- [ ] Botón de favoritos en todas las páginas de detalle
- [ ] Página `/mis-favoritos` para usuarios

### Otros
- [ ] Página `/sobre-nosotros`
- [ ] Página `/contacto`
- [ ] Página `/terminos-y-condiciones`
- [ ] Página `/politica-de-privacidad`

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
- [ ] Crear `/admin/dashboard`:
  - [ ] Total de usuarios (tourists, socios)
  - [ ] Total de contenido (locales, eventos, etc.)
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
- [ ] Modelo `Booking` (reserva)
- [ ] Estado: pendiente, confirmada, cancelada
- [ ] Relación con experiencias
- [ ] Panel de gestión de reservas para socios
- [ ] Notificaciones por email

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
- [ ] Botones de compartir (Facebook, Twitter, WhatsApp)
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
- [ ] GitHub Actions workflow:
  - [ ] Ejecutar tests automáticamente
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
- [ ] Skeleton loaders en lugar de spinners
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
