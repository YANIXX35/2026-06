#!/bin/bash
set -e

# ── Ecrire les variables d'environnement Render dans .env ─────────────────────
# Ceci garantit que config:cache utilise les vraies valeurs Render
echo "==> Injection des variables d environnement..."

cat > .env << EOF
APP_NAME="${APP_NAME:-AntiGaspiCI}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"
DB_SSLMODE="${DB_SSLMODE:-require}"

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER="${MAIL_MAILER:-log}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-noreply@antigaspi-ci.com}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-AntiGaspiCI}"

GEMINI_API_KEY="${GEMINI_API_KEY}"
GOOGLE_CLIENT_ID="${GOOGLE_CLIENT_ID}"
GOOGLE_CLIENT_SECRET="${GOOGLE_CLIENT_SECRET}"
GOOGLE_REDIRECT_URI="${GOOGLE_REDIRECT_URI}"
FACEBOOK_CLIENT_ID="${FACEBOOK_CLIENT_ID}"
FACEBOOK_CLIENT_SECRET="${FACEBOOK_CLIENT_SECRET}"

TELESCOPE_ENABLED=false
EOF

# ── Verifier APP_KEY ───────────────────────────────────────────────────────────
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]]; then
    echo '==> WARNING : APP_KEY absent, generation automatique...'
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
    echo '==> WARNING : Migration echouee. Le serveur demarre quand meme.'
fi

echo '==> Lien symbolique storage...'
php artisan storage:link 2>/dev/null || echo 'Storage link deja present.'

echo '==> Demarrage du serveur PHP...'
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
