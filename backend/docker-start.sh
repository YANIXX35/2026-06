#!/bin/bash
set -e

echo "==> Migration base de données..."
php artisan migrate --force

echo "==> Lien storage..."
php artisan storage:link || true

echo "==> Cache production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Démarrage Apache..."
apache2-foreground
