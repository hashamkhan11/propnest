# Production image: nginx + PHP-FPM, with the frontend built in a separate stage.
# See docker/entrypoint.d for what runs when the container starts.

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
# Tailwind scans the pagination views inside vendor/.
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

FROM serversideup/php:8.3-fpm-nginx

USER root
RUN install-php-extensions gd intl exif
COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/
USER www-data

COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=vendor /app/vendor /var/www/html/vendor
COPY --chown=www-data:www-data --from=assets /app/public/build /var/www/html/public/build

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PHP_OPCACHE_ENABLE=1
