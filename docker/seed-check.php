<?php

use App\Models\Biota;
use Illuminate\Contracts\Console\Kernel;

// Keluar dengan kode 0 jika data biota sudah ada (artinya seeder tidak perlu dijalankan lagi),
// kode 1 jika masih kosong. Dipakai entrypoint agar seeder tidak mengulang dan mereset XP pengguna.

require '/var/www/html/vendor/autoload.php';

$app = require '/var/www/html/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

exit(Biota::query()->exists() ? 0 : 1);
