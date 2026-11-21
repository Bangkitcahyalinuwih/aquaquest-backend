<?php

return [
    // Disk penyimpanan gambar unggahan. 'public' = storage/app/public (lokal).
    // Ganti lewat .env (GAMBAR_DISK) bila memakai penyimpanan eksternal bergaya S3.
    'gambar_disk' => env('GAMBAR_DISK', 'public'),
    // Kata sandi akun seed. Di server publik WAJIB diganti lewat SEED_PASSWORD.
    'seed_password' => env('SEED_PASSWORD', 'password'),
];
