#!/bin/bash
set -e

echo "==> Construction du fichier .env depuis les variables Render..."

# Utilise printf pour eviter les problemes de caracteres speciaux dans les mots de passe
{
printf 'APP_NAME=%s\n'    "${APP_NAME:-AntiGaspiCI}"
printf 'APP_ENV=%s\n'     "${APP_ENV:-production}"
printf 'APP_KEY=%s\n'     "${APP_KEY}"
printf 'APP_DEBUG=%s\n'   "${APP_DEBUG:-false}"
printf 'APP_URL=%s\n'     "${APP_URL:-http://localhost}"
printf 'APP_LOCALE=fr\n'
printf 'APP_FALLBACK_LOCALE=fr\n'
printf 'LOG_CHANNEL=stderr\n'
printf 'LOG_LEVEL=info\n'
printf 'DB_CONNECTION=%s\n'  "${DB_CONNECTION:-pgsql}"
printf 'DB_HOST=%s\n'        "${DB_HOST}"
printf 'DB_PORT=%s\n'        "${DB_PORT:-5432}"
printf 'DB_DATABASE=%s\n'    "${DB_DATABASE}"
printf 'DB_USERNAME=%s\n'    "${DB_USERNAME}"
printf 'DB_PASSWORD=%s\n'    "${DB_PASSWORD}"
printf 'DB_SSLMODE=%s\n'     "${DB_SSLMODE:-require}"
printf 'SESSION_DRIVER=database\n'
printf 'SESSION_LIFETIME=120\n'
printf 'BROADCAST_CONNECTION=log\n'
printf 'FILESYSTEM_DISK=public\n'
printf 'QUEUE_CONNECTION=sync\n'
printf 'CACHE_STORE=file\n'
printf 'MAIL_MAILER=%s\n'        "${MAIL_MAILER:-log}"
printf 'MAIL_HOST=%s\n'          "${MAIL_HOST:-smtp.gmail.com}"
printf 'MAIL_PORT=%s\n'          "${MAIL_PORT:-587}"
printf 'MAIL_USERNAME=%s\n'      "${MAIL_USERNAME}"
printf 'MAIL_PASSWORD=%s\n'      "${MAIL_PASSWORD}"
printf 'MAIL_ENCRYPTION=%s\n'    "${MAIL_ENCRYPTION:-tls}"
printf 'MAIL_FROM_ADDRESS=%s\n'  "${MAIL_FROM_ADDRESS:-noreply@antigaspi-ci.com}"
printf 'MAIL_FROM_NAME=%s\n'     "${MAIL_FROM_NAME:-AntiGaspiCI}"
printf 'GEMINI_API_KEY=%s\n'     "${GEMINI_API_KEY}"
printf 'CLOUDINARY_URL=%s\n'    "${CLOUDINARY_URL}"
printf 'GOOGLE_CLIENT_ID=%s\n'   "${GOOGLE_CLIENT_ID}"
printf 'GOOGLE_CLIENT_SECRET=%s\n' "${GOOGLE_CLIENT_SECRET}"
printf 'GOOGLE_REDIRECT_URI=%s\n'  "${GOOGLE_REDIRECT_URI}"
printf 'FACEBOOK_CLIENT_ID=%s\n'   "${FACEBOOK_CLIENT_ID}"
printf 'FACEBOOK_CLIENT_SECRET=%s\n' "${FACEBOOK_CLIENT_SECRET}"
printf 'TELESCOPE_ENABLED=false\n'
} > .env

echo "==> Valeurs DB detectees :"
echo "    DB_HOST       = ${DB_HOST:-[VIDE - AJOUTER DANS RENDER ENV]}"
echo "    DB_DATABASE   = ${DB_DATABASE:-[VIDE]}"
echo "    DB_USERNAME   = ${DB_USERNAME:-[VIDE]}"
echo "    APP_KEY       = ${APP_KEY:0:20}..."
echo "==> Valeurs MAIL detectees :"
echo "    MAIL_MAILER   = ${MAIL_MAILER:-log}"
echo "    MAIL_HOST     = ${MAIL_HOST:-[VIDE]}"
echo "    MAIL_USERNAME = ${MAIL_USERNAME:-[VIDE - emails ne seront pas envoyes]}"
echo "==> Valeurs IA detectees :"
echo "    GEMINI_API_KEY = ${GEMINI_API_KEY:0:10}... (longueur: ${#GEMINI_API_KEY} chars)"

# Verifier APP_KEY
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]]; then
    echo '==> WARNING : APP_KEY absent, generation automatique...'
    php artisan key:generate --force || true
fi

# Verifier DB
if [ -z "$DB_HOST" ]; then
    echo '==> ERREUR CRITIQUE : DB_HOST vide !'
    echo '==> Ajoutez DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD dans Render > Environment'
fi

echo '==> Cache configuration...'
php artisan config:clear
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

echo '==> Seeding categories (firstOrCreate - idempotent)...'
php artisan db:seed --class=CategorieSeeder --force || echo 'WARNING: Seeder categories echoue.'

echo '==> Seeding admin (firstOrCreate - idempotent)...'
php artisan db:seed --class=AdminSeeder --force || echo 'WARNING: Seeder admin echoue.'

echo '==> Lien symbolique storage...'
php artisan storage:link 2>/dev/null || echo 'Storage link deja present.'

echo '==> Demarrage du scheduler (expiration annonces, etc.)...'
php artisan schedule:work --no-interaction > /dev/null 2>&1 &

echo '==> Demarrage du serveur PHP...'
# `php artisan serve` est mono-thread par defaut : une requete lente (ex. le chat
# IA qui garde une connexion streaming ouverte) bloque toutes les autres, ce qui
# peut expliquer un timeout observe sur /annonces/{id}. PHP_CLI_SERVER_WORKERS
# fait tourner plusieurs workers en parallele (supporte nativement par le serveur
# CLI de PHP depuis PHP 8.0 / Laravel 9.3+). Ce n'est toujours pas un serveur de
# production a part entiere (pas de reverse proxy, pas de gestion fine des
# ressources) : a remplacer par php-fpm+nginx ou FrankenPHP/Octane apres la
# soutenance si le trafic le justifie.
export PHP_CLI_SERVER_WORKERS=${PHP_CLI_SERVER_WORKERS:-4}
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
