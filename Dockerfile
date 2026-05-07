# Usamos Apache para un despliegue sencillo, robusto y auto-contenido
FROM php:8.3-apache

# 1. Instalar dependencias esenciales del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar y habilitar extensiones de PHP (incluyendo soporte para Redis)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configurar Apache para servir desde la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Instalar Composer (Herramienta oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar los archivos del proyecto (incluyendo la carpeta public/build que generas en tu local)
COPY . .

# 6. Instalar dependencias de PHP para producción (optimiza clases y excluye development)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Asignar permisos correctos para que Apache pueda escribir en cache y logs
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto expuesto estándar de Apache
EXPOSE 80

# 8. Copiar y sanitizar el script de entrada (entrypoint.sh)
COPY entrypoint.sh /usr/local/bin/

# ESTA LÍNEA ELIMINA EL ERROR '\r: command not found' DE FORMA INFALIBLE:
# Convierte el formato CRLF a LF y asigna permisos de ejecución dentro del contenedor.
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
