#!/bin/sh
# Prepares the app each time the container starts.
#
# With SQLite on a host without a persistent disk (like a free Render web
# service), every start is a fresh database. Set SEED_DEMO=true and the demo
# marketplace is rebuilt on each start, so the live demo resets itself.
set -e
cd /var/www/html

fresh_database=false

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    database="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$database" ]; then
        touch "$database"
        fresh_database=true
    fi
fi

php artisan migrate --force --no-interaction

if [ "$fresh_database" = "true" ] && [ "${SEED_DEMO:-false}" = "true" ]; then
    echo "Seeding demo marketplace..."
    php artisan db:seed --class=DemoSeeder --force --no-interaction
fi

php artisan storage:link --force --no-interaction

# A throwaway demo can run without a fixed key; sessions end on restart anyway.
# `optimize` caches the config, which is how PHP-FPM sees the generated key.
if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is not set, generating one for this container."
    APP_KEY="$(php artisan key:generate --show --no-interaction)"
    export APP_KEY
fi

php artisan optimize --no-interaction
