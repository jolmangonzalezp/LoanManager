# Usamos la imagen oficial de PHP con Apache
FROM php:8.3-apache

# 1. Instalar dependencias del sistema esenciales
RUN apt-get update && apt-get install -y \
    git curl libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libicu-dev libonig-dev libxml2-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP (incluye Redis para tu arquitectura)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip intl mbstring pdo_mysql bcmath sockets \
    && pecl install redis && docker-php-ext-enable redis

# 3. Configurar Apache para servir desde /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 5. Copiar el código del proyecto
# Importante: Asegúrate de que la carpeta public/build esté en tu repo
COPY . .

# 6. Instalar dependencias de producción de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Permisos de escritura para el motor de Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. PREPARACIÓN DEL ENTRYPOINT (En la ruta de la app para evitar bloqueos de sistema)
COPY entrypoint.sh /var/www/html/entrypoint.sh

# Limpiamos caracteres de Windows, damos permisos y asignamos dueño
RUN sed -i 's/\r$//' /var/www/html/entrypoint.sh && \
    chmod +x /var/www/html/entrypoint.sh && \
    chown www-data:www-data /var/www/html/entrypoint.sh

EXPOSE 80

# Ejecutamos el script desde la carpeta de la aplicación
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
