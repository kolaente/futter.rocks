FROM composer:2.7 AS build-php

WORKDIR /app
COPY ./ ./
RUN composer install --optimize-autoloader --ignore-platform-req=php --ignore-platform-req=ext-*

FROM node:22 AS build-frontend

WORKDIR /app

COPY ./package.json ./
COPY ./pnpm-lock.yaml ./
COPY ./.npmrc ./

RUN corepack enable && \
    pnpm install

# To make tailwind purge find templates from vendor
COPY --from=build-php /app/vendor /app/vendor
COPY ./ ./

RUN pnpm run build && \
    rm -rf node_modules

FROM kolaente/laravel:8.3-octane-frankenphp

RUN apt-get update && apt-get install -y libpq-dev postgresql-client && \
  docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
  docker-php-ext-install pdo_pgsql pgsql

COPY ./ ./
COPY --from=build-frontend /app/public /app/public
COPY --from=build-php /app/vendor /app/vendor
