#!/bin/bash
set -e

echo "==> Migration de la base de données..."
php artisan migrate --force

echo "==> Lien symbolique storage..."
php artisan storage:link || true

echo "==> Nettoyage du cache..."
php artisan optimize:clear

echo "==> Démarrage du serveur PHP..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
