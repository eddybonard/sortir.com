# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.0.12
ARG NGINX_VERSION=1.17


# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine3.13 AS api_platform_php

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

ARG APCU_VERSION=5.1.18
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		zlib-dev \
		libxslt-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_mysql \
		zip \
		xsl \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/api-platform.prod.ini $PHP_INI_DIR/conf.d/api-platform.ini


# Workaround to allow using PHPUnit 8 with Symfony 4.3
ENV SYMFONY_PHPUNIT_VERSION=8.3
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/api

# build for production
ARG APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    SYMFOMY_ENV=prod composer install --prefer-dist --no-scripts --no-autoloader --no-interaction --no-ansi --no-dev --ignore-platform-req=ext-sockets

COPY .env ./

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY templates templates/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev  --optimize; \
	composer run-script --no-dev post-install-cmd; \
    bin/console cache:clear --no-warmup && \
    bin/console cache:warmup


COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chown -R www-data:www-data var/cache && \
    chown -R www-data:www-data var/log && \
    rm -rf docker


RUN chmod +x /usr/local/bin/docker-entrypoint

# Cleanup
RUN apk del git \
    && find / -type f -iname \*.apk-new -delete \
    && rm -rf /tmp/* /var/cache/apk/* /root/.composer

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
