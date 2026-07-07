# Utiliser l'image PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires + zip/unzip + outils de débogage
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql \
    && docker-php-ext-enable pdo_pgsql \
    && docker-php-ext-enable pgsql

# Copier une configuration PHP personnalisée (optionnel)
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Activer le module Rewrite d'Apache
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . /var/www/html/

# Afficher les extensions chargées pour vérification (dans les logs de build)
RUN php -m | grep pdo_pgsql || (echo "pdo_pgsql NOT FOUND" && exit 1)

# Installer les dépendances PHP
RUN composer install --no-dev --no-interaction

# Configurer Apache pour servir le site
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80