# Deployment Checklist - Turiscovery

## Pre-Deployment

### Código
- [ ] Todo el código está en el repositorio Git
- [ ] No hay credenciales hardcodeadas en el código
- [ ] `.env.example` está actualizado
- [ ] `.gitignore` está configurado correctamente
- [ ] `composer.lock` y `package-lock.json` están en el repo

### Testing
- [ ] La aplicación funciona correctamente en local
- [ ] Las migraciones se ejecutan sin errores
- [ ] Los seeders funcionan (opcional)
- [ ] API endpoints responden correctamente
- [ ] Frontend carga y funciona
- [ ] Mapas Leaflet funcionan
- [ ] Sistema de favoritos funciona
- [ ] Sistema de reviews funciona

### Preparación
- [ ] Servidor contratado y accesible via SSH
- [ ] Dominio registrado
- [ ] DNS apuntando al servidor
- [ ] Google OAuth credentials obtenidas
- [ ] Email SMTP configurado (opcional)

---

## Durante Deployment

### Servidor
- [ ] PHP 8.2+ instalado
- [ ] PostgreSQL 14+ con PostGIS instalado
- [ ] Nginx configurado
- [ ] Node.js instalado
- [ ] Composer instalado
- [ ] Redis instalado (opcional)

### Base de Datos
- [ ] Base de datos creada
- [ ] Usuario PostgreSQL creado
- [ ] PostGIS extension habilitada
- [ ] Conexión funciona desde Laravel

### Aplicación
- [ ] Código clonado en `/var/www/turiscovery`
- [ ] `.env` configurado con valores de producción
- [ ] `APP_KEY` generado
- [ ] Dependencias instaladas (`composer install --no-dev`)
- [ ] Assets compilados (`npm run build`)
- [ ] Migraciones ejecutadas
- [ ] Storage link creado
- [ ] Permisos correctos (www-data)

### SSL/HTTPS
- [ ] Certificado SSL obtenido (Let's Encrypt)
- [ ] Nginx configurado para HTTPS
- [ ] Redirección HTTP → HTTPS activa
- [ ] Certificado válido y funcionando

### Optimización
- [ ] OPcache habilitado
- [ ] `config:cache` ejecutado
- [ ] `route:cache` ejecutado
- [ ] `view:cache` ejecutado
- [ ] Gzip habilitado en Nginx
- [ ] Cache headers configurados

---

## Post-Deployment

### Verificación
- [ ] Sitio web accesible via HTTPS
- [ ] Homepage carga correctamente
- [ ] Navegación entre páginas funciona
- [ ] Login/Registro funciona
- [ ] Google OAuth funciona
- [ ] API responde correctamente
- [ ] Dashboard de socios accesible
- [ ] Crear contenido funciona
- [ ] Mapas se muestran correctamente
- [ ] Imágenes cargan

### Seguridad
- [ ] Firewall (UFW) activado
- [ ] Solo puertos necesarios abiertos
- [ ] Fail2Ban instalado
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Contraseñas fuertes en todo

### Monitoreo
- [ ] Logs accesibles
- [ ] Backups automáticos configurados
- [ ] Espacio en disco monitoreado
- [ ] Certificado SSL con renovación automática
- [ ] Email notifications configurado (opcional)

### Performance
- [ ] Tiempo de carga < 3 segundos
- [ ] Lighthouse score > 80
- [ ] No errores en consola del navegador
- [ ] API response times < 500ms

---

## Mantenimiento Continuo

### Diario
- [ ] Verificar logs de errores
- [ ] Verificar uso de recursos (CPU/RAM/Disco)

### Semanal
- [ ] Revisar backups
- [ ] Actualizar dependencias de seguridad

### Mensual
- [ ] Actualizar sistema operativo
- [ ] Revisar analytics
- [ ] Limpiar logs antiguos

---

## Rollback Plan

Si algo sale mal:

```bash
# 1. Activar modo mantenimiento
php artisan down

# 2. Restaurar código anterior
git reset --hard COMMIT_ANTERIOR

# 3. Restaurar base de datos
psql turiscovery < backup.sql

# 4. Limpiar caché
php artisan config:clear
php artisan cache:clear

# 5. Desactivar mantenimiento
php artisan up
```

---

## Contactos de Emergencia

- **Hosting Support**: [Número/Email]
- **Domain Registrar**: [Número/Email]
- **Developer**: [Tu contacto]

---

✅ **Deployment completado exitosamente cuando todos los checkboxes están marcados**
