FROM php:8.2-apache

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql

# Activer l'extension dans le php.ini d'Apache
RUN echo "extension=pdo_pgsql" > /usr/local/etc/php/conf.d/20-pdo_pgsql.ini \
    && echo "extension=pgsql" >> /usr/local/etc/php/conf.d/20-pdo_pgsql.ini

# Activer le module Rewrite d'Apache
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers
COPY . /var/www/html/
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
# Installer les dépendances
RUN composer install --no-dev --no-interaction

# Vérifier que l'extension est chargée (dans les logs)
RUN php -m | grep pdo_pgsql || (echo "pdo_pgsql NOT FOUND" && exit 1)

# Configurer Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80