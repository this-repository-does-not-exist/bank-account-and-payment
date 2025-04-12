FROM php:8.4-cli-alpine
COPY --from=composer/composer:2-bin /composer /usr/local/bin/composer
WORKDIR /app
