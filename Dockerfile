FROM php:8.3-apache

# 1. Dependencias
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Código (incluyendo public/build compilado en tu Arch Linux)
COPY . .

# 6. Limpieza y Optimización
RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    rm -rf bootstrap/cache/*.php && \
    mkdir -p bootstrap/cache && \
    chmod -R 775 bootstrap/cache

# 7. Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage

EXPOSE 80

# 8. Comando de arranque con espera
# Intentamos limpiar caché y migrar. Si la DB no responde, fallará solo la migración pero Apache intentará subir.
CMD ["sh", "-c", "php artisan optimize:clear && php artisan migrate --force && apache2-foreground"]
