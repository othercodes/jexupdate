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
COPY var/ /app/var

RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative

FROM php:7.4-apache

RUN apt update && apt upgrade -y
COPY infrastructure/prod/docker/start.sh /usr/local/bin/start
RUN echo "ServerName 127.0.0.1" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && chmod +x /usr/local/bin/start

COPY --from=dockyard /app /opt/othercode/jexserver

RUN rm -rf /var/www/html \
    && ln -s /opt/othercode/jexserver/public /var/www/html \
    && chown -R www-data:www-data /opt/othercode/jexserver/var

ENTRYPOINT ["/usr/local/bin/start"]
