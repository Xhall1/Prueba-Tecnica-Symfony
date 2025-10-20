FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev unzip git \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

RUN a2enmod rewrite

COPY . /var/www/html/
WORKDIR /var/www/html

ENV APP_ENV=prod
ENV APP_DEBUG=0

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

EXPOSE 80
CMD ["apache2-foreground"]
