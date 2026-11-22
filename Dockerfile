# syntax=docker/dockerfile:1.7

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

FROM php:8.4-apache AS app

COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql gd intl zip bcmath exif opcache \
    && a2enmod rewrite headers \
    && printf 'ServerTokens Prod\nServerSignature Off\n' > /etc/apache2/conf-available/security-extra.conf \
    && a2enconf security-extra

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-aquaquest.ini"
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
COPY docker/seed-check.php /usr/local/bin/seed-check.php
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -fsS http://localhost:${PORT:-80}/up || exit 1

ENV PORT=80
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf \
    && rm -rf /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage \
    && ln -s /tmp/storage /var/www/html/storage \
    && ln -s /tmp/bootstrap/cache /var/www/html/bootstrap/cache \
    && ln -s /tmp/storage/app/public /var/www/html/public/storage
    
RUN sed -i 's|/var/run/apache2|/tmp/apache2|g' /etc/apache2/envvars \
    && sed -i 's|/var/lock/apache2|/tmp/apache2/lock|g' /etc/apache2/envvars

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]