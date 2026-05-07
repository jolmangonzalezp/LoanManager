#!/bin/bash

# Generar caché de configuración para velocidad
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Correr migraciones automáticamente
echo "Ejecutando migraciones en la base de datos..."
php artisan migrate --force

# Iniciar Apache en primer plano
exec apache2-foreground
