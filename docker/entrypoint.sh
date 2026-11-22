#!/bin/sh
# Dijalankan setiap kontainer mulai. Menyiapkan aplikasi lalu menyerahkan proses ke Apache.
set -e

if [ "$1" = "apache2-foreground" ]; then
    cd /var/www/html

    if [ -z "$APP_KEY" ]; then
        echo "[entrypoint] ERROR: APP_KEY belum diisi." >&2
        exit 1
    fi

    export PORT="${PORT:-80}"

    # Buat direktori writable di /tmp (target symlink dari storage & bootstrap/cache)
   mkdir -p /tmp/storage/framework/cache/data \
             /tmp/storage/framework/sessions \
             /tmp/storage/framework/views \
             /tmp/storage/logs \
             /tmp/storage/app/public \
             /tmp/bootstrap/cache

    cp -rn /opt/storage-public/. /tmp/storage/app/public/ 2>/dev/null || true

    if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
        echo "[entrypoint] menjalankan migrasi database..."
        tries=0
        until php artisan migrate --force; do
            tries=$((tries + 1))
            if [ "$tries" -ge 10 ]; then echo "[entrypoint] ERROR: migrasi gagal." >&2; exit 1; fi
            echo "[entrypoint] database belum siap, ulangi dalam 3 detik ($tries/10)..."
            sleep 3
        done
    fi

    if [ "${AUTO_SEED:-false}" = "true" ]; then
        if php /usr/local/bin/seed-check.php; then
            echo "[entrypoint] data sudah ada, seeder dilewati."
        else
            echo "[entrypoint] database kosong, menjalankan seeder..."
            php artisan db:seed --force
        fi
    fi

    php artisan config:cache
    php artisan route:cache || echo "[entrypoint] route:cache dilewati."

    mkdir -p /tmp/apache2/lock
    chmod -R 775 /tmp/storage /tmp/bootstrap/cache /tmp/apache2 2>/dev/null || true
fi

exec "$@"