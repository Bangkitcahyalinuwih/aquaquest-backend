<?php

// Keluar dengan kode 0 jika data biota sudah ada (artinya seeder tidak perlu dijalankan lagi),
// kode 1 jika masih kosong. Dipakai entrypoint agar seeder tidak mengulang dan mereset XP pengguna.

require '/var/www/html/vendor/autoload.php';

$app = require '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

exit(\App\Models\Biota::query()->exists() ? 0 : 1);
