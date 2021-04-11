FROM php:7.4-apache

RUN apt update && apt upgrade -y
COPY infrastructure/dev/docker/start.sh /usr/local/bin/start
RUN echo "ServerName 127.0.0.1" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && chmod +x /usr/local/bin/start

RUN apt install zip unzip \
    && pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug \
    && bash -c 'mkdir -p /opt/othercode/jexserver/{public,var}'

RUN bash -c 'mkdir -p /.composer' \
    && chmod 777 /.composer \
    && curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN rm -rf /var/www/html \
    && ln -s /opt/othercode/jexserver/public /var/www/html \
    && chown -R www-data:www-data /opt/othercode/jexserver/var

ENTRYPOINT ["/usr/local/bin/start"]