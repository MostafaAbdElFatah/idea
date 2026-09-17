FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev libpq-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip intl bcmath \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000
CMD php artisan storage:link --force; php artisan optimize \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
