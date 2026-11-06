# AquaQuest Backend (API)

API Laravel 12 untuk AquaQuest. Kontrak: [docs/KONTRAK-API.md](docs/KONTRAK-API.md). Koleksi Postman: `docs/postman/`.

## Menjalankan

composer install
cp .env.example .env && php artisan key:generate
(buat database aquaquest*api, sesuaikan DB*\* di .env)
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve # http://localhost:8000

## Uji

php artisan test && vendor/bin/pint --test
