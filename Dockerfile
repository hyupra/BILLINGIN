FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
      gnupg2 curl ca-certificates unixodbc-dev libzip-dev unzip git \
 && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
      | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
 && echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
      > /etc/apt/sources.list.d/mssql-release.list \
 && apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
 && pecl install sqlsrv-5.13.0 pdo_sqlsrv-5.13.0 \
 && docker-php-ext-enable sqlsrv pdo_sqlsrv \
 && docker-php-ext-install pcntl bcmath zip opcache \
 && pecl install redis && docker-php-ext-enable redis \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
# Empty, not secret: real config comes from docker-compose env_file at
# runtime (which phpdotenv never overrides). Its only job is to stop
# phpdotenv's file_get_contents(.env) from failing — that failure is
# suppressed but PHPUnit still flags it as a risky-test warning on every
# test, since Laravel's test bootstrap tries to safeLoad .env each time.
RUN touch .env
# ponytail: dev-mode install (keeps Faker/Pest for seeders+tests); add a --no-dev
# prod build path (build arg or separate Dockerfile) when an actual deploy happens
RUN composer install --optimize-autoloader --no-interaction

# php-fpm workers run as www-data (see php-fpm.d/www.conf), but COPY leaves
# everything root-owned — Laravel couldn't write compiled views, logs, or
# cache, failing with "tempnam(): file created in the system's temporary
# directory" on the very first request. storage/logs doesn't even ship in
# git (Laravel relies on Monolog auto-creating it), so create it explicitly
# too rather than hope the app process has permission to.
RUN mkdir -p storage/logs \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache
