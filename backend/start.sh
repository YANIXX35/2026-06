#!/bin/bash
set -e

# Verifier APP_KEY
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]]; then
    echo '==> WARNING : APP_KEY absent ou invalide, generation automatique...'
    php artisan key:generate --force || true
fi

echo '==> Cache configuration...'
php artisan config:cache

echo '==> Cache vues...'
php artisan view:clear
php artisan view:cache

echo '==> Migration base de donnees...'
if php artisan migrate --force; then
    echo '==> Migrations OK.'
else
    echo '==> WARNING : Migration echouee (DB non disponible ?). Le serveur demarre quand meme.'
fi

echo '==> Lien symbolique storage...'
php artisan storage:link 2>/dev/null || echo 'Storage link deja present.'

echo '==> Demarrage du serveur PHP...'
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
