FROM composer AS dockyard

WORKDIR /app
COPY composer.json composer.lock /app/
RUN composer install \
    --ignore-platform-reqs \
    --no-ansi \
    --no-autoloader \
    --no-dev \
    --no-interaction \
    --no-scripts

COPY LICENSE.md /app/
COPY .env.example /app/.env
COPY app/ /app/app
COPY public/ /app/public
COPY src/ /app/src
COPY var/   /app/var

RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative

FROM php:7.4-apache

RUN apt update && apt upgrade -y
RUN a2enmod rewrite
COPY --from=dockyard /app /opt/jexserver
RUN rm -rf /var/www/html \
    && ln -s /opt/jexserver/public /var/www/html \
    && chown -R www-data:www-data /opt/jexserver/var

WORKDIR /var/www/html
