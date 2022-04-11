FROM php:8.0.0-fpm

RUN apt-get update && apt-get install -y git
#In this place you can specify all the extensions you need.
RUN docker-php-ext-install pdo_mysql

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

RUN  apt-get install git unzip wait-for-it wget libicu-dev -y && \
        docker-php-ext-install intl pdo_mysql && \
        echo 'extension=intl.so' > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini

RUN pecl install xdebug-3.1.3 \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libxml2-dev \
        librabbitmq-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp

COPY --from=composer:2.0.9 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
