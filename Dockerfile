FROM php:8.2-cli

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs zip unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Create temporary .env for build
RUN cp .env.example .env

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN php artisan key:generate

RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache
RUN chmod +x /var/www/docker-entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["/var/www/docker-entrypoint.sh"]
