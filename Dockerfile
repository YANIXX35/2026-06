FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev libpng-dev libxml2-dev libonig-dev libicu-dev libpq-dev curl unzip git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring zip gd xml bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY backend/ .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN chmod +x start.sh

EXPOSE 8000

CMD ["bash", "start.sh"]
