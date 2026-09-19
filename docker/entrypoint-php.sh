#!/bin/sh

set -o allexport
source $ENV_FILE_PATH
set +o allexport

# The MySQL server presents its auto-generated, self-signed certificate. Since
# MariaDB 11.4 the client bundled in Alpine verifies the server certificate by
# default, which fails with "ERROR 2026 (HY000): TLS/SSL error: self-signed
# certificate in certificate chain". The connection stays TLS encrypted and
# never leaves the internal overlay network, so skip the chain verification.
MYSQL="mysql --skip-ssl-verify-server-cert"

# Wait for the database to be ready
echo "Waiting for database to be ready..."
while ! $MYSQL $DB_DATABASE -h$DB_HOST -u $DB_USERNAME --password=$DB_PASSWORD --silent; do
    echo "... still waiting"
    sleep 1
done

# Migration and startup
php artisan migrate --force --no-interaction

# Seed Database if necessary
USER_COUNT=$($MYSQL $DB_DATABASE -h$DB_HOST -u $DB_USERNAME --password=$DB_PASSWORD \
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
