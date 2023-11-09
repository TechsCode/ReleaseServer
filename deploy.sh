#!/bin/bash
set -e

echo "Deployment started ..."
echo "-----------------------------------"

# Enter maintenance mode or return true
# if already is in maintenance mode
echo "1/11 Entering maintenance mode"
(php8.1 artisan down) || true

# Stash all changes
echo "2/11 Stash all changes"
git stash

# Pull the latest version of the app
echo "3/11 Pull the latest version of the app"
git pull origin production

# Pop the stashed changes
echo "4/11 Pop the stashed changes"
git stash pop

# Install composer dependencies
echo "5/11 Install composer dependencies"
/usr/bin/php8.1 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
echo "6/11 Clear the old cache"
php8.1 artisan clear-compiled

# Recreate cache
echo "7/11 Recreate cache"
php8.1 artisan optimize

# Compile npm assets
echo "8/11 Install npm dependencies"
npm install --no-progress --prefer-dist

# Compile npm assets
echo "9/11 Compile npm assets"
npm run prod

# Run database migrations
echo "10/11 Run database migrations"
php8.1 artisan migrate --force

# Exit maintenance mode
echo "11/11 Exit maintenance mode"
php8.1 artisan up

echo "Deployment finished!"
