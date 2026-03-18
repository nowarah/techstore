FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    icu-libs \
    libpq \
    libzip

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo_pgsql \
        zip \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ------- deps -------
FROM base AS deps

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# ------- production -------
FROM base AS production

COPY --from=deps /app /app

RUN mkdir -p var/cache var/log config/jwt \
    && chown -R www-data:www-data var config/jwt

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
