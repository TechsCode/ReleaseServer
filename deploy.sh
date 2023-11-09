#!/bin/bash
set -e

echo "Deployment started ..."
echo "-----------------------------------"

# Enter maintenance mode or return true
# if already is in maintenance mode
echo "1/8 Entering maintenance mode"
(php8.1 artisan down) || true

# Pull the latest version of the app
echo "2/8 Pull the latest version of the app"
git pull origin production

# Install composer dependencies
echo "3/8 Install composer dependencies"
/usr/bin/php8.1 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
echo "4/8 Clear the old cache"
php8.1 artisan clear-compiled

# Recreate cache
echo "5/8 Recreate cache"
php8.1 artisan optimize

# Compile npm assets
echo "6/8 Compile npm assets"
npm run prod

# Run database migrations
echo "7/8 Run database migrations"
php8.1 artisan migrate --force

# Exit maintenance mode
echo "8/8 Exit maintenance mode"
php8.1 artisan up

echo "Deployment finished!"
