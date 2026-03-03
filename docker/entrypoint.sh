#!/bin/sh
set -e

cd /var/www/html

echo "==> Configurando ambiente..."

# Gera .env a partir das variáveis de ambiente do Fly.io (secrets)
cat > .env <<EOF
APP_NAME="${APP_NAME:-Agenda}"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL:-https://agenda-app.fly.dev}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-db.qgwsayaobbatoqupsgpp.supabase.co}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-smtp.gmail.com}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Startup de Bolso}"

JWT_SECRET=${JWT_SECRET}
JWT_ALGO=${JWT_ALGO:-HS256}

API_KEY=${API_KEY}
EOF

echo "==> Limpando caches..."
php artisan config:clear
php artisan cache:clear 2>/dev/null || true

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Iniciando servidor..."
exec "$@"
