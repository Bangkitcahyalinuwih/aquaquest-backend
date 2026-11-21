#!/bin/sh
# Dijalankan setiap kontainer mulai. Menyiapkan aplikasi lalu menyerahkan proses ke Apache.
set -e

if [ "$1" = "apache2-foreground" ]; then
    cd /var/www/html

    if [ -z "$APP_KEY" ]; then
        echo "[entrypoint] ERROR: APP_KEY belum diisi." >&2
        exit 1
    fi

    # Banyak layanan hosting memberi nomor port lewat variabel PORT. Apache disetel mengikutinya (bawaan: 80).
    PORT="${PORT:-80}"
    sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

    mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public
    php artisan storage:link --force >/dev/null 2>&1 || true

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

    chown -R www-data:www-data storage bootstrap/cache
fi

exec "$@"