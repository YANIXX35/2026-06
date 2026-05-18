#!/bin/bash
set -e

echo '==> Migration base de donnees...'
php artisan migrate --force

echo '==> Lien symbolique storage...'
php artisan storage:link || true

echo '==> Demarrage du serveur PHP...'
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
