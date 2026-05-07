#!/bin/bash

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Ejecutando migraciones en la base de datos..."
php artisan migrate --force

exec apache2-foreground
