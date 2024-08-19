#!/bin/sh

set -o allexport
source $ENV_FILE_PATH
set +o allexport

# Rename environment variables to match the ones used by the MySQL image
export MYSQL_ROOT_PASSWORD=$DB_ROOT_PASSWORD
export MYSQL_DATABASE=$DB_DATABASE
export MYSQL_USER=$DB_USERNAME
export MYSQL_PASSWORD=$DB_PASSWORD
export MYSQL_ALLOW_EMPTY_PASSWORD=1
export MYSQL_ROOT_HOST="%"

# RUN entrypoint
source ./entrypoint.sh mysqld
