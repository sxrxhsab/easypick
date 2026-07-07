FROM php:8.2-apache

# Installer les extensions MySQL + outils
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo_mysql

# Activer le module Rewrite d'Apache
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers
COPY . /var/www/html/

# Installer les dépendances PHP
RUN composer install --no-dev --no-interaction

# ✅ Créer le dossier uploads et donner les droits
RUN mkdir -p /var/www/html/uploads && chmod -R 755 /var/www/html/uploads

# Configurer Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80