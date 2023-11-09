#!/bin/bash
set -e

echo "Deployment started ..."
echo "-----------------------------------"

# Enter maintenance mode or return true
# if already is in maintenance mode
echo "1/9 Entering maintenance mode"
(php8.1 artisan down) || true

# Pull the latest version of the app
echo "2/9 Pull the latest version of the app"
git pull origin production

# Install composer dependencies
echo "3/9 Install composer dependencies"
/usr/bin/php8.1 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
echo "4/9 Clear the old cache"
php8.1 artisan clear-compiled

# Recreate cache
echo "5/9 Recreate cache"
php8.1 artisan optimize

# Compile npm assets
echo "6/9 Install npm dependencies"
npm install --no-progress --prefer-dist

# Compile npm assets
echo "7/9 Compile npm assets"
npm run prod

# Run database migrations
echo "8/9 Run database migrations"
php8.1 artisan migrate --force

# Exit maintenance mode
echo "9/9 Exit maintenance mode"
php8.1 artisan up

echo "Deployment finished!"
