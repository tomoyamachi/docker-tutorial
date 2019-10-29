FROM php:7.3.0-fpm-alpine
RUN apk --no-cache add icu-dev autoconf make g++ gcc
RUN docker-php-ext-install  -j$(nproc) iconv intl mbstring pdo_mysql
RUN { \
  echo 'short_open_tag = On'; \
  echo 'fastcgi.logging = 1'; \
} > /usr/local/etc/php/conf.d/overrides.ini