#------- BASE -------
FROM php:8.4.14-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
    ssh \
    git \
    sudo \
    cron \
    locales \
    libzip-dev \
    libgmp-dev \
    libicu-dev \
    libpq-dev \
    unzip \
    nano \
    iputils-ping \
    htop


RUN docker-php-ext-install zip \
    pdo \
    pdo_pgsql \
    intl gmp opcache sockets \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable opcache

RUN curl -fL --output /usr/local/bin/pie https://github.com/php/pie/releases/download/1.4.10/pie.phar \
    && chmod +x /usr/local/bin/pie \
    && pie install --auto-install-build-tools phpredis/phpredis:6.2.0

# Locale
RUN sed -i -e \
  's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen \
   && locale-gen

ENV LANG ru_RU.UTF-8
ENV LANGUAGE ru_RU:ru
ENV LC_LANG ru_RU.UTF-8
ENV LC_ALL ru_RU.UTF-8

ENV TZ="UTC"

COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

ENV PATH="$PATH:/var/www/vendor/bin:/var/www/bin"

ENTRYPOINT ["/bin/bash", "/var/www/bin/entrypoint.sh"]
CMD ["php-fpm"]

COPY ./docker/php/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY ./docker/php/symfony-fpm-pool.conf $PHP_INI_DIR/../php-fpm.d/www.conf

#Копируем файлы проекта в докер
COPY ./composer.json /var/www/composer.json
COPY ./composer.lock /var/www/composer.lock
COPY ./symfony.lock /var/www/symfony.lock
COPY ./bin/ /var/www/bin/
COPY ./public/ /var/www/public/
COPY ./config/ /var/www/config/
COPY ./.env /var/www/.env
COPY ./.env.test /var/www/.env.test
#COPY ./migrations /var/www/migrations
COPY ./src/ /var/www/src/

ARG GIT_TOKEN
ENV GIT_TOKEN=$GIT_TOKEN

RUN git config --global url."https://${GIT_TOKEN}@github.com/".insteadOf "git@github.com:" && \
    git config --global url."https://${GIT_TOKEN}@github.com/".insteadOf "https://github.com/" && \
    composer install --prefer-dist --no-scripts --no-dev --optimize-autoloader --classmap-authoritative && \
    chmod +x /var/www/bin/entrypoint.sh && \
    rm -rf /var/www/html

#------- END BASE -------

#------- DEV -------
FROM base AS dev

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"
ENV APP_ENV=dev

RUN pie install --auto-install-build-tools xdebug/xdebug:3.4.7

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"  && \
    echo 'user = root' >> $PHP_INI_DIR/../php-fpm.d/www.conf  && \
    echo 'group = root' >> $PHP_INI_DIR/../php-fpm.d/www.conf && \
    composer install --prefer-dist --no-scripts --optimize-autoloader

COPY ./.php-cs-fixer.dist.php /var/www/.php-cs-fixer.dist.php
COPY ./phpstan.neon.dist /var/www/phpstan.neon.dist
COPY ./phpunit.xml /var/www/phpunit.xml
COPY ./psalm.xml /var/www/psalm.xml
COPY ./tests /var/www/tests
COPY ./docker/php/xdebug.ini $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

#------ END DEV--------

#------- PROD -------
FROM base AS prod

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
ENV PHP_OPCACHE_PRELOAD="/var/www/config/preload.php"
ENV APP_ENV=prod

RUN mkdir -p /var/www/var/cache/prod && \
    chown -R www-data:www-data /var/www && \
    echo "www-data   ALL=NOPASSWD: /usr/sbin/cron" >> /etc/sudoers  && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    apt-get -y clean > /dev/null && \
    echo 'user = www-data' >> $PHP_INI_DIR/../php-fpm.d/www.conf && \
    echo 'group = www-data' >> $PHP_INI_DIR/../php-fpm.d/www.conf

USER www-data:www-data

#------ END PROD--------
