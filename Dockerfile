FROM php:8.4-apache

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP (Esenciales para MariaDB, Redis y Cálculos de Préstamos)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configuración de Apache y Puerto dinámico
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# Definimos el puerto por defecto para evitar que Apache falle si la variable está vacía
ENV PORT 80

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    # Corrección crítica: Usamos una sintaxis más robusta para el puerto
    && sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/*.conf \
    && a2enmod rewrite

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar código fuente
# IMPORTANTE: Debes haber corrido 'npm run build' en tu Arch local antes de este paso
COPY . .

# 6. Instalación de dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# 7. Gestión de permisos (Específico para www-data de Apache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# 8. Comando de arranque optimizado para Cloud
# Cacheamos configuración y rutas para evitar procesamientos innecesarios en cada petición
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && apache2-foreground"]
