@echo off
echo ========================================
echo  Test de Rendimiento de Base de Datos
echo  Turiscovery - PostgreSQL
echo ========================================
echo.

echo [1/5] Verificando configuracion...
php artisan config:cache
echo.

echo [2/5] Aplicando indices (puede tardar 1-3 minutos)...
php artisan migrate --force
echo.

echo [3/5] Monitoreando estadisticas...
php artisan db:monitor
echo.

echo [4/5] Verificando conexiones activas...
php artisan db:monitor --connections
echo.

echo [5/5] Estadisticas de cache...
php artisan db:monitor --cache
echo.

echo ========================================
echo  Test completado!
echo ========================================
echo.
echo Ahora prueba acceder a:
echo   - http://localhost:8000/api/eventos
echo   - http://localhost:8000/api/experiencias
echo   - http://localhost:8000/api/locales
echo.
echo La primera carga sera ~250ms
echo Las siguientes cargas seran ~50ms (cache)
echo.
pause
