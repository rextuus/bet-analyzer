# syntax=docker/dockerfile:1
# bet-analyzer – Produktions-Images (compose.prod.yaml). Die Entwicklungsumgebung (docker-compose.yml) ist
# davon unabhängig.
#
#   base    PHP-FPM 8.5 mit den nötigen Erweiterungen
#   vendor  Composer-Abhängigkeiten (ohne dev)
#   prod    App-Image für app, worker, cron und migrate; baut die Assets (AssetMapper + Sass) → public/assets
#   web     Caddy mit public/ aus prod
FROM php:8.5.13-fpm-alpine AS base

RUN apk add --no-cache icu-libs icu-data-full libzip fcgi tzdata \
 && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev linux-headers \
 && docker-php-ext-install -j"$(nproc)" intl pdo_mysql zip bcmath \
 && pecl install apcu \
 && docker-php-ext-enable apcu \
 && apk del .build-deps

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer
COPY docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/zz-app.ini
COPY docker/php/php-fpm.d/zz-app.conf /usr/local/etc/php-fpm.d/zz-app.conf

ENV TZ=Europe/Berlin
WORKDIR /srv/app

FROM base AS vendor
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --no-autoloader

FROM base AS prod
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && curl -fsSLo /usr/local/bin/supercronic \
      https://github.com/aptible/supercronic/releases/download/v0.2.51/supercronic-linux-amd64 \
 && echo "cc232108374e8e2b537045f2d5eaa1229f619db314c3ba6a14d38b35dc0aa123  /usr/local/bin/supercronic" | sha256sum -c - \
 && chmod +x /usr/local/bin/supercronic
ENV APP_ENV=prod
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /srv/app/vendor vendor
# Die .env ist absichtlich nicht im Repo (lokal enthält sie Secrets, .dockerignore hält sie fern). Hier nur
# ungefährliche Standardwerte; alles Echte kommt zur Laufzeit aus dem Environment (compose.prod.yaml).
# Die Dummy-URL gilt nur für diese Befehle und landet nicht in .env.local.php.
RUN printf '%s\n' \
      'APP_ENV=prod' \
      'APP_SECRET=' \
      'DATABASE_URL=' \
      'MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0' \
      'MAILER_DSN=null://null' \
      'TELEGRAM_DSN=null://null' \
      'TELEGRAM_ENABLED=0' \
      'SENTRY_DSN=' \
      'CASH_BOX_MIN_LIMIT=80' \
      'BACKUP_SERVER_IP=' \
      'TRUSTED_PROXIES=127.0.0.1' \
      > .env \
 && composer dump-autoload --no-dev --classmap-authoritative \
 && composer dump-env prod \
 && export DATABASE_URL='mysql://build:build@127.0.0.1:3306/build?serverVersion=8.4.13&charset=utf8mb4' \
 && php bin/console assets:install public \
 && php bin/console importmap:install \
 && php bin/console sass:build \
 && php bin/console asset-map:compile \
 && php bin/console cache:warmup \
 && mkdir -p public/backups \
 && chown -R www-data:www-data var public/backups
# public/backups = Volume mit den JSON-Rohdaten der Buchmacher (siehe compose.prod.yaml). Der Ordner gehört
# www-data, damit ein neues Volume diese Rechte übernimmt.
USER www-data

# Caddy mit den öffentlichen Dateien (gebaute Assets) aus dem Produktions-Image – ohne public/backups.
FROM caddy:2.11.9-alpine AS web
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
COPY --from=prod /srv/app/public /srv/app/public
RUN rm -rf /srv/app/public/backups
