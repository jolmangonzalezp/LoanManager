FROM php:8.4-apache

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP (Incluye las necesarias para MariaDB y Redis)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configuración de Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 5. Instalación de dependencias (IGNORANDO límites de plataforma para evitar el error de la 8.5)
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 6. Permisos correctos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 7. Comando de arranque: Limpiar caché, migrar y encender Apache
# CMD ["sh", "-c", "php artisan config:clear && php artisan cache:clear && php artisan package:discover --ansi && php artisan migrate --force && apache2-foreground"]
# Cambia tu CMD por este:
CMD ["apache2-foreground"]
