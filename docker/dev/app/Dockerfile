FROM php:8.1.7-fpm-alpine

LABEL maintainer="Nazar Taran"

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY php.ini /usr/local/etc/php/
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && apk del pcre-dev ${PHPIZE_DEPS}

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data ; exit 0

CMD php artisan key:generate && \
    php-fpm
