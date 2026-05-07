#!/bin/bash
set -e

echo "Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Iniciando Apache..."
exec apache2-foreground
