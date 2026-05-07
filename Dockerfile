FROM php:8.3-apache

# 1. Instalación de dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones de PHP necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configuración de Apache para Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar código (Asegúrate de haber ejecutado 'npm run build' localmente)
COPY . .

# 6. Dependencias de producción de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Permisos de carpetas (Crucial para Laravel)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Limpieza del Entrypoint y permisos
# Forzamos formato Linux y permisos de ejecución
RUN sed -i 's/\r$//' entrypoint.sh && \
    chmod +x entrypoint.sh && \
    chown www-data:www-data entrypoint.sh

EXPOSE 80

# Invocamos con 'sh' para asegurar compatibilidad en entornos PaaS
ENTRYPOINT ["sh", "/var/www/html/entrypoint.sh"]
