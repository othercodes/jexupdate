FROM php:7.4-apache

RUN apt update && apt upgrade -y
RUN pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug
RUN a2enmod rewrite

RUN mkdir -p /.composer && chmod 777 /.composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN bash -c 'mkdir -p /opt/jexserver/{public,var}' \
    && rm -rf /var/www/html \
    && ln -s /opt/jexserver/public /var/www/html \
    && chown -R www-data:www-data /opt/jexserver/var

ENV COMPOSER_ALLOW_SUPERUSER=1