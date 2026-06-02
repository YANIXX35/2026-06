#!/bin/bash
set -e

# Verifier que APP_KEY est un vrai cle Laravel (format base64:...)
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]]; then
    echo '==> ERREUR : APP_KEY manquant ou invalide.'
    echo '==> Generez une cle avec : php artisan key:generate --show'
    echo '==> Puis ajoutez-la dans Render > Environment > APP_KEY'
    echo '==> Generation automatique d une cle temporaire...'
    php artisan key:generate --force || true
fi

echo '==> Cache configuration...'
php artisan config:cache

echo '==> Cache vues...'
php artisan view:cache

echo '==> Migration base de donnees...'
php artisan migrate --force

echo '==> Lien symbolique storage...'
php artisan storage:link 2>/dev/null || echo 'Storage link deja present.'

echo '==> Demarrage du serveur PHP...'
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
