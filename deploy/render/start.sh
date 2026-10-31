#!/bin/sh
set -eu
: "${APP_KEY:?Falta configurar APP_KEY}"
: "${DB_URL:?Falta configurar la conexión de base de datos}"
# Render supplies a persistent random encryption key without Laravel's prefix.
case "$APP_KEY" in base64:*) ;; *) export APP_KEY="base64:$APP_KEY" ;; esac
if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
    export ASSET_URL="$RENDER_EXTERNAL_URL"
fi
case "${PORT:-10000}" in *[!0-9]*|'') echo 'Puerto inválido' >&2; exit 1 ;; esac
sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT:-10000}>/" /etc/apache2/sites-available/000-default.conf
# Release steps run before accepting HTTP requests, including on the free plan.
su -s /bin/sh www-data -c 'php artisan config:cache && php artisan migrate --force && php artisan academic:prepare-presentation && php artisan view:cache'
exec apache2-foreground
