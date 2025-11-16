# Dockerfile (ubicación: ./Dockerfile)
# Imagen base PHP 8.1 con Apache
FROM php:8.1-apache

# Habilitar mod_rewrite para poder usar front controller (.htaccess)
RUN a2enmod rewrite

# Instalar extensiones necesarias para PDO MySQL y utilidades
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
 && docker-php-ext-install pdo pdo_mysql zip

# Copiar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar todo el proyecto al contenedor
COPY . /var/www/html

# Cambiar el DocumentRoot de Apache a la carpeta public/
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Dar permisos básicos al proyecto (ajustable según entorno)
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Instalar dependencias de Composer dentro del contenedor
RUN composer install --no-interaction --no-dev --optimize-autoloader || true

# Exponer puerto 80 del contenedor
EXPOSE 80

# Comando para levantar Apache en primer plano
CMD ["apache2-foreground"]
