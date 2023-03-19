FROM php:8.1-fpm-alpine

# Instala mysql
RUN docker-php-ext-install pdo_mysql

# Dependencias para XDEBUG
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    gcc \
    g++ \
    make \
    autoconf \
    libc-dev \
    re2c \
    linux-headers

# Instala XEDBUG
RUN pecl install -o -f xdebug-3.1.5 \
&& docker-php-ext-enable xdebug