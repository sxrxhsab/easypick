# Utiliser l'image PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires + zip/unzip
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql

# Copier la configuration PHP personnalisée
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Activer le module Rewrite d'Apache
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . /var/www/html/

# Installer les dépendances PHP
RUN composer install --no-dev --no-interaction

# Configurer Apache pour servir le site
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80