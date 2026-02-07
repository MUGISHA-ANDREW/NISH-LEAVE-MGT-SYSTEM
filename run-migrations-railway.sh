#!/bin/bash

# This script ensures migrations run on Railway
# Add this to your railway.json or Procfile if needed

echo "Running database migrations..."
php artisan migrate --force

echo "Migrations completed!"
echo "Tables in database:"
php artisan db:show --counts
