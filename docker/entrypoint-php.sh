#!/bin/sh

set -o allexport
source $ENV_FILE_PATH
set +o allexport

php artisan migrate --force --no-interaction
php-fpm --nodaemonize
