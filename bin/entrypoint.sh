#!/bin/bash

set -e


if [ `ls vendor | wc -l` -eq 0 ] && [ "$APP_ENV" = "dev" ]; then
    composer install --prefer-dist --no-scripts --optimize-autoloader
fi

if [ "$APP_ENV" = "prod" ]; then
    console dump:env;
fi


case $1 in
    php-fpm)
        echo "Run fpm mode"

        console assets:install --no-interaction

        if [ "$APP_ENV" = "dev" ];
            then COMMAND="docker-php-entrypoint php-fpm --allow-to-run-as-root"
            else COMMAND="docker-php-entrypoint php-fpm"
        fi
    ;;

    cli)
        echo "Run cli mode"

        console cron:setup --no-interaction

        COMMAND='sudo /usr/sbin/cron -f'
    ;;

    *)
        COMMAND=$@
    ;;
esac


exec  $COMMAND
