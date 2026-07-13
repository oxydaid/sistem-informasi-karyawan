#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Wait for database connection if host is specified
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database on $DB_HOST:$DB_PORT..."
    # A simple loop to check if database port is open (optional but helpful)
    for i in {1..30}; do
        if timeout 1 bash -c "cat < /dev/null > /dev/tcp/$DB_HOST/${DB_PORT:-3306}" 2>/dev/null; then
            echo "Database is up!"
            break
        fi
        echo "Database not ready yet, retrying in 2 seconds... ($i/30)"
        sleep 2
    done
fi

# Run Laravel optimizations
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link --no-interaction || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Check if a custom command was passed to the container
if [ $# -gt 0 ]; then
    echo "Running custom command..."
    exec "$@"
fi

# Start processes via Supervisor
echo "Starting services via Supervisor..."
exec supervisord -c /etc/supervisor/supervisord.conf
