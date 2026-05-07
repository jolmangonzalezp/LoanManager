FROM php:8.3-apache

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones de PHP
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

# 5. Copiar código
COPY . .

# 6. Dependencias de PHP y OPTIMIZACIÓN EN BUILD
# Ejecutamos las cachés aquí para que el contenedor arranque más rápido
RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    php artisan config:clear && \
    php artisan route:clear

# 7. Permisos de carpetas
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 8. CMD en lugar de ENTRYPOINT
# Usamos el formato de lista para que Apache sea el proceso principal (PID 1)
# Si necesitas correr migraciones, Sevalla permite ejecutarlas como un "Pre-deploy command"
# Si no, las ejecutaremos aquí de la forma más simple posible:
CMD ["sh", "-c", "php artisan migrate --force && apache2-foreground"]
