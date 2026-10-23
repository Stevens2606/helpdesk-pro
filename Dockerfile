FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libsqlite3-dev libpng-dev libonig-dev nodejs npm \
    && docker-php-ext-install pdo_sqlite zip gd mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci && npm run build

RUN touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

EXPOSE 10000
CMD php artisan migrate:fresh --seed --force \
    && php artisan storage:link \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}