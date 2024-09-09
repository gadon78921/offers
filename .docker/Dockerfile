FROM php:8.1-fpm-alpine3.14 as app
WORKDIR /

# Add S6-overlay init system
ADD https://gitlab.7733.ru/devops/s6/raw/master/s6-overlay-amd64.tar.gz /
RUN chmod 777 s6-overlay-amd64.tar.gz
RUN tar xzf s6-overlay-amd64.tar.gz && rm -rf s6-overlay-amd64.tar.gz

# Install php-ext
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pcntl pdo_pgsql opcache redis-5.3.4
RUN ln -fs /usr/local/bin/php /usr/bin/php

RUN apk add --no-cache \
    nginx \
    gettext \
    tzdata \
    ca-certificates \
    bash \
    procps \
    nano \
    git \
    openssh \
    sudo \
    postgresql-client

# Fix "sudo: setrlimit(RLIMIT_CORE): Operation not permitted" message
RUN echo "Set disable_coredump false" > /etc/sudo.conf
# Fix timezone
RUN ln -snf /usr/share/zoneinfo/Asia/Vladivostok /etc/localtime && echo Asia/Vladivostok > /etc/timezone
# Install xdebug
ARG WITH_XDEBUG=false
RUN if [ $WITH_XDEBUG = "true" ] ; then \
    install-php-extensions xdebug-3.1.1; \
fi
# php settings
COPY ./.docker/php.ini /usr/local/etc/php/php.ini
COPY ./.docker/www.conf /usr/local/etc/php-fpm.d/

COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/vhost.conf /etc/nginx/conf.d/default.conf


COPY . /service/
COPY --from=composer:lts /usr/bin/composer /usr/bin/composer

WORKDIR /service
RUN composer install --ignore-platform-reqs --no-cache --optimize-autoloader --no-progress

COPY --chown=www-data:www-data . /service/
RUN mkdir -p /service/var/cache/prod/
RUN chown -R www-data:www-data /service/var
RUN chmod +x /service/bin/console

RUN mkdir /tmp/prices/ /tmp_file && \
    chown www-data:www-data -R /tmp/prices/ /tmp_file /var/lib/nginx/


FROM nginx:stable-alpine as nginx

WORKDIR /service

COPY --from=app /service/ /service/
COPY .docker/nginx.conf /etc/nginx/nginx.conf
COPY .docker/vhost.conf /etc/nginx/conf.d/default.conf