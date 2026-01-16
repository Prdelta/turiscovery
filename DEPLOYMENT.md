# 🚀 Guía de Deployment - Turiscovery

## Requisitos del Servidor

### Mínimos Recomendados
- **OS**: Ubuntu 22.04 LTS o similar
- **RAM**: 2GB mínimo (4GB recomendado)
- **CPU**: 2 cores
- **Disco**: 20GB SSD
- **PHP**: 8.2+
- **PostgreSQL**: 14+ con PostGIS
- **Node.js**: 18+
- **Nginx o Apache**

---

## 📋 Paso 1: Preparar el Servidor

### 1.1 Actualizar Sistema
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Instalar PHP y Extensiones
```bash
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-pgsql \
    php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl \
    php8.2-zip php8.2-gd php8.2-intl php8.2-redis
```

### 1.3 Instalar PostgreSQL con PostGIS
```bash
sudo apt install -y postgresql postgresql-contrib postgis postgresql-14-postgis-3

# Iniciar servicio
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### 1.4 Instalar Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 1.5 Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 1.6 Instalar Nginx
```bash
sudo apt install -y nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

---

## 🗄️ Paso 2: Configurar Base de Datos

### 2.1 Crear Usuario y Base de Datos
```bash
sudo -u postgres psql

-- Dentro de PostgreSQL:
CREATE USER turiscovery WITH PASSWORD 'TU_PASSWORD_SEGURO_AQUI';
CREATE DATABASE turiscovery OWNER turiscovery;
GRANT ALL PRIVILEGES ON DATABASE turiscovery TO turiscovery;

-- Habilitar PostGIS
\c turiscovery
CREATE EXTENSION postgis;

-- Salir
\q
```

### 2.2 Configurar Acceso Remoto (si es necesario)
```bash
sudo nano /etc/postgresql/14/main/pg_hba.conf

# Añadir línea:
host    turiscovery    turiscovery    0.0.0.0/0    md5

# Reiniciar PostgreSQL
sudo systemctl restart postgresql
```

---

## 📦 Paso 3: Clonar y Configurar la Aplicación

### 3.1 Clonar Repositorio
```bash
cd /var/www
sudo git clone https://tu-repositorio.git turiscovery
cd turiscovery
```

### 3.2 Configurar Permisos
```bash
sudo chown -R www-data:www-data /var/www/turiscovery
sudo chmod -R 775 storage bootstrap/cache
```

### 3.3 Copiar Variables de Entorno
```bash
cp .env.example .env
nano .env
```

### 3.4 Configurar `.env` para Producción
```env
APP_NAME=Turiscovery
APP_ENV=production
APP_KEY=  # Se generará con php artisan key:generate
APP_DEBUG=false
APP_TIMEZONE=America/Lima
APP_URL=https://turiscovery.com

LOG_CHANNEL=daily
LOG_LEVEL=warning

# Base de Datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=turiscovery
DB_USERNAME=turiscovery
DB_PASSWORD=TU_PASSWORD_SEGURO_AQUI

# Redis para cache y sesiones (opcional pero recomendado)
BROADCAST_CONNECTION=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Sanctum
SANCTUM_STATEFUL_DOMAINS=turiscovery.com,www.turiscovery.com

# Google OAuth
GOOGLE_CLIENT_ID=tu_google_client_id
GOOGLE_CLIENT_SECRET=tu_google_client_secret
GOOGLE_REDIRECT_URI=https://turiscovery.com/api/auth/google/callback

# Mail (configurar según tu proveedor)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@turiscovery.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.5 Instalar Dependencias
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3.6 Configurar Aplicación
```bash
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force  # Solo si quieres datos de ejemplo
php artisan storage:link
```

---

## 🌐 Paso 4: Configurar Nginx

### 4.1 Crear Configuración de Sitio
```bash
sudo nano /etc/nginx/sites-available/turiscovery
```

### 4.2 Contenido del Archivo
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name turiscovery.com www.turiscovery.com;
    
    # Redirigir a HTTPS (después de configurar SSL)
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name turiscovery.com www.turiscovery.com;
    
    root /var/www/turiscovery/public;
    index index.php index.html;
    
    # SSL Certificates (configurar con Let's Encrypt más adelante)
    ssl_certificate /etc/letsencrypt/live/turiscovery.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/turiscovery.com/privkey.pem;
    
    # SSL Configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Logs
    access_log /var/log/nginx/turiscovery_access.log;
    error_log /var/log/nginx/turiscovery_error.log;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript 
               application/x-javascript application/xml+rss 
               application/javascript application/json;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Laravel Routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Static Assets Cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

### 4.3 Habilitar Sitio
```bash
sudo ln -s /etc/nginx/sites-available/turiscovery /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔒 Paso 5: Configurar SSL con Let's Encrypt

### 5.1 Instalar Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 5.2 Obtener Certificado
```bash
sudo certbot --nginx -d turiscovery.com -d www.turiscovery.com
```

### 5.3 Configurar Renovación Automática
```bash
sudo certbot renew --dry-run
```

---

## ⚡ Paso 6: Optimizaciones de Performance

### 6.1 Instalar y Configurar Redis
```bash
sudo apt install -y redis-server
sudo systemctl start redis
sudo systemctl enable redis

# Configurar Laravel para usar Redis
php artisan config:cache
```

### 6.2 Configurar PHP-FPM
```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf

# Ajustar valores:
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

# Reiniciar
sudo systemctl restart php8.2-fpm
```

### 6.3 Configurar OPcache
```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini

# Añadir/modificar:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

---

## 🔄 Paso 7: Configurar Queue Workers (Opcional)

### 7.1 Crear Servicio Systemd
```bash
sudo nano /etc/systemd/system/turiscovery-worker.service
```

```ini
[Unit]
Description=Turiscovery Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/turiscovery/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

### 7.2 Habilitar Worker
```bash
sudo systemctl daemon-reload
sudo systemctl start turiscovery-worker
sudo systemctl enable turiscovery-worker
```

---

## 🔐 Paso 8: Seguridad Adicional

### 8.1 Firewall
```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw allow 5432  # PostgreSQL (solo si acceso remoto)
sudo ufw enable
```

### 8.2 Fail2Ban
```bash
sudo apt install -y fail2ban
sudo systemctl start fail2ban
sudo systemctl enable fail2ban
```

### 8.3 Actualizar Contraseñas
- Cambiar contraseña de base de datos
- Rotar `APP_KEY` si es necesario
- Usar contraseñas fuertes para todos los servicios

---

## 📊 Paso 9: Monitoreo y Backups

### 9.1 Configurar Backups de Base de Datos
```bash
# Crear script de backup
sudo nano /usr/local/bin/backup-turiscovery.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/turiscovery"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup de PostgreSQL
pg_dump -h localhost -U turiscovery turiscovery | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Eliminar backups antiguos (más de 7 días)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-turiscovery.sh

# Agregar a crontab (diario a las 2 AM)
sudo crontab -e
0 2 * * * /usr/local/bin/backup-turiscovery.sh
```

### 9.2 Logs
```bash
# Ver logs de aplicación
tail -f storage/logs/laravel.log

# Ver logs de Nginx
tail -f /var/log/nginx/turiscovery_error.log

# Ver logs de PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

---

## 🔄 Paso 10: Proceso de Actualización

Cuando necesites actualizar la aplicación:

```bash
cd /var/www/turiscovery

# Activar modo mantenimiento
php artisan down

# Obtener últimos cambios
git pull origin main

# Actualizar dependencias
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Actualizar aplicación
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
sudo systemctl restart php8.2-fpm
sudo systemctl restart turiscovery-worker  # Si usas workers

# Desactivar modo mantenimiento
php artisan up
```

---

## ✅ Checklist Pre-Launch

- [ ] Variables de entorno configuradas correctamente
- [ ] SSL/HTTPS funcionando
- [ ] Base de datos con PostGIS habilitada
- [ ] Migraciones ejecutadas
- [ ] Assets compilados (`npm run build`)
- [ ] Caché de configuración generado
- [ ] Permisos correctos en `storage/` y `bootstrap/cache/`
- [ ] Firewall configurado
- [ ] Backups automáticos configurados
- [ ] Google OAuth credenciales obtenidas
- [ ] Dominio apuntando al servidor
- [ ] Logs monitoreados
- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production`

---

## 🆘 Troubleshooting

### Error 500
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Limpiar caché
php artisan cache:clear
php artisan config:clear
```

### Problemas con PostGIS
```bash
# Verificar extensión
sudo -u postgres psql turiscovery -c "SELECT PostGIS_Version();"
```

### Permisos
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📱 Contacto y Soporte

- **Documentación Laravel**: https://laravel.com/docs
- **PostGIS**: https://postgis.net/documentation

¡Turiscovery está listo para producción! 🚀🏔️
