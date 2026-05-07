# Usamos Apache para simplificar la infraestructura (Todo en uno)
FROM php:8.3-apache

# 1. Instalar dependencias del sistema y NODE.JS (Necesario para compilar Vue)
RUN apt-get update && apt-get install -y \
    git curl wget unzip libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP (Incluyendo Redis)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configurar Apache para Laravel (Apuntar a /public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar archivos y preparar la APP
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node y COMPILAR VUE (Vite)
RUN npm install && npm run build

# 6. Permisos correctos para Linux
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Exponer puerto 80 (Apache)
EXPOSE 80

# 8. Script de inicio (Entrypoint) para correr migraciones en Runtime
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
