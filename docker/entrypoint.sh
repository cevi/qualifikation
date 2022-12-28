#!/bin/sh

php artisan migrate
php-fpm --nodaemonize
