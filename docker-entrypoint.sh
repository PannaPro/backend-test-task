#!/bin/sh

set -e

echo 'Waiting for database to be ready...'

ATTEMPTS_LEFT_TO_REACH_DATABASE=60
until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
    echo "Still waiting for database... $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
done

if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo 'Database is not reachable.'
    exit 1
fi

echo 'Ensuring database exists...'
php bin/console doctrine:database:create --if-not-exists --no-interaction || true

MIGRATION_TABLE_EXISTS=$(php bin/console dbal:run-sql -q "SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'doctrine_migration_versions'" | tr -dc '0-9')

if [ -n "$(find ./migrations -iname '*.php' -print -quit)" ]; then
    php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing
fi

if [ "${MIGRATION_TABLE_EXISTS:-0}" -eq 0 ]; then
    echo 'Loading fixtures...'
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo 'Skipping fixtures.'
fi

php bin/console cache:clear
php bin/console -V

echo 'PHP app ready!'

exec docker-php-entrypoint "$@"