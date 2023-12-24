#!/bin/bash
set -e

echo "Deployment started ..."
echo "-----------------------------------"

# Enter maintenance mode or return true
# if already is in maintenance mode
echo "1/12 Entering maintenance mode"
(php artisan down) || true

# Stash all changes
echo "2/12 Stash all changes"
git stash

# Pull the latest version of the app
echo "3/12 Pull the latest version of the app"
git pull origin production

# Pop the stashed changes
echo "4/12 Pop the stashed changes"
git stash pop

# Install composer dependencies
echo "5/12 Install composer dependencies"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
echo "6/12 Clear the old cache"
php artisan clear-compiled

# Recreate cache
echo "7/12 Recreate cache"
php artisan optimize

# Compile npm assets
echo "8/12 Install npm dependencies"
npm install --no-progress --prefer-dist

# Compile npm assets
echo "9/12 Compile npm assets"
npm run prod

# Run database migrations
echo "10/12 Run database migrations"
php artisan migrate --force

# Recreate cache
echo "11/12 Recreate cache"
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

# Exit maintenance mode
echo "12/12 Exit maintenance mode"
php artisan up

echo "Deployment finished!"
