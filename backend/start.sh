#!/bin/bash
set -e

echo "==> Migration de la base de donnees..."
php artisan migrate --force

echo "==> Lien symbolique storage..."
php artisan storage:link || true

echo "==> Cache de production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Demarrage du serveur PHP..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}