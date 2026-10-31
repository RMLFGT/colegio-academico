FROM php:8.3-apache AS base
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libonig-dev libicu-dev libzip-dev unzip \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql mbstring intl zip bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' > /etc/apache2/conf-available/academic.conf \
    && a2enconf academic \
    && printf '<IfModule mpm_prefork_module>\nStartServers 2\nMinSpareServers 1\nMaxSpareServers 3\nMaxRequestWorkers 5\nMaxConnectionsPerChild 1000\n</IfModule>\n' > /etc/apache2/mods-available/mpm_prefork.conf
WORKDIR /var/www/html

FROM base AS dependencies
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY app/ ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress

FROM base AS production
COPY --from=dependencies --chown=www-data:www-data /var/www/html /var/www/html
COPY deploy/render/start.sh /usr/local/bin/start-academic
RUN chmod +x /usr/local/bin/start-academic \
    && printf 'display_errors=Off\nlog_errors=On\nexpose_php=Off\n' > /usr/local/etc/php/conf.d/production.ini
EXPOSE 10000
CMD ["/usr/local/bin/start-academic"]
