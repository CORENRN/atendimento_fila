FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libldap2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install pdo_mysql ldap

WORKDIR /var/www
COPY . /var/www

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache