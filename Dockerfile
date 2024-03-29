ARG PHP_VERSION
FROM php:${PHP_VERSION}-cli-alpine

RUN apk add --no-cache make && \
    docker-php-ext-enable opcache && \
    docker-php-source delete

RUN echo $'\
display_errors=On\n\
error_reporting=E_ALL\n\
date.timezone=UTC\n\
' >> /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -s https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet && \
    mv composer.phar /usr/local/bin/composer && \
    echo $'export PATH="$HOME/.composer/vendor/bin:$PATH"\n' >> /root/.profile
