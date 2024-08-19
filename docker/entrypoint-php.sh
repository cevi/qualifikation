#!/bin/sh

set -o allexport
source $ENV_FILE_PATH
set +o allexport

# Wait for the database to be ready
echo "Waiting for database to be ready..."
while ! mysql $DB_DATABASE -h$DB_HOST -u $DB_USERNAME --password=$DB_PASSWORD --silent; do
    echo "... still waiting"
    sleep 1
done

# Migration and startup
php artisan migrate --force --no-interaction

# Seed Database if necessary
USER_COUNT=$(mysql $DB_DATABASE -h$DB_HOST -u $DB_USERNAME --password=$DB_PASSWORD \
                --silent -e "SELECT COUNT(*) FROM users WHERE username = 'tn11@demo'" | sed -n 1p )

echo "User count: $USER_COUNT"
if [ $USER_COUNT -eq 1 ]; then
    echo "Database is already seeded"
else
    echo "Database is not seeded, seed database"
    composer install
    php artisan migrate --force --no-interaction --seed
fi

php-fpm --nodaemonize
