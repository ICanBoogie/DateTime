ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-cli-trixie

RUN <<-EOF
	apt-get update
	apt-get install -y autoconf pkg-config unzip
	pecl channel-update pecl.php.net
	pecl install xdebug
	docker-php-ext-enable xdebug
EOF

RUN <<-EOF
	cat <<-SHELL >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
	xdebug.client_host=host.docker.internal
	xdebug.mode=develop
	xdebug.start_with_request=yes
	SHELL

	cat <<-SHELL >> /usr/local/etc/php/conf.d/php.ini
	display_errors=On
	error_reporting=E_ALL
	date.timezone=UTC
	SHELL
EOF

# composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# lint

RUN composer global require friendsofphp/php-cs-fixer
