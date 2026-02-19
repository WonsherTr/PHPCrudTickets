#!/bin/bash
set -e

cd /var/www/html

# Generate APP_KEY if not set (check both env var and .env file)
CURRENT_KEY=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d'=' -f2-)
if [ -z "$CURRENT_KEY" ] || [ "$CURRENT_KEY" = "" ]; then
    echo "Generating APP_KEY..."
    # Ensure clean APP_KEY line exists
    sed -i '/^APP_KEY=/d' .env
    echo "APP_KEY=" >> .env
    php artisan key:generate --force
fi

# Wait for database to be ready
echo "Waiting for database..."
max_tries=30
count=0
until php artisan migrate:status > /dev/null 2>&1 || [ $count -eq $max_tries ]; do
    echo "DB not ready yet... retrying in 2s ($count/$max_tries)"
    sleep 2
    count=$((count + 1))
done

if [ $count -eq $max_tries ]; then
    echo "WARNING: Could not connect to database after $max_tries attempts"
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed if needed (only if users table is empty)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Cache config & routes for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting application..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
