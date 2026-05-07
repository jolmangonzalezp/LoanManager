FROM php:8.4-apache

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP (Optimizadas para MariaDB, Redis y Cálculos de Préstamos)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configuración elástica de Apache para Cloud (SeValla)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    # Esto permite que Apache escuche en el puerto que SeValla le asigne dinámicamente
    && sed -i "s/Listen 80/Listen \${PORT:-80}/g" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:\${PORT:-80}>/g" /etc/apache2/sites-available/*.conf \
    && a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar archivos y preparar dependencias
# Nota: Asegúrate de haber corrido 'npm run build' en tu Arch antes de desplegar
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 6. Permisos de Producción
# En SeValla usamos www-data para que Apache pueda escribir logs y caché
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 7. Comando de arranque para Producción
# Limpiamos y cacheamos configuración para máximo rendimiento en el cloud
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && apache2-foreground"]
