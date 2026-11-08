<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\KartuService;
use App\Services\LevelService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (khusus lokal/demo). Password: password
        $this->buat('Admin Dinas Kelautan', 'admin@aquaquest.test', 'admin', 0);

        // XP 80: satu sesi kuis (maks +50) langsung memicu naik level dan membuka kartu baru saat demo.
        $this->buat('Demo Pengguna', 'demo@aquaquest.test', 'user', 80);

        $pemain = [
            ['Budi Santoso', 420],
            ['Siti Aminah', 360],
            ['Rizky Pratama', 310],
            ['Dewi Lestari', 250],
            ['Andi Wijaya', 180],
            ['Maya Putri', 120],
            ['Fajar Nugroho', 60],
            ['Intan Permata', 30],
        ];

        foreach ($pemain as $i => [$nama, $xp]) {
            $this->buat($nama, 'pemain'.($i + 1).'@aquaquest.test', 'user', $xp);
        }
    }

    private function buat(string $nama, string $email, string $role, int $xp): void
    {
        // forceFill: kolom role/total_xp/level sengaja tidak masuk $fillable demi keamanan.
        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $nama,
            'password' => Hash::make('password'),
            'role' => $role,
            'total_xp' => $xp,
            'level' => app(LevelService::class)->hitungLevel($xp),
            'email_verified_at' => now(),
        ])->save();

        if ($role === 'user') {
            app(KartuService::class)->bukaKartuBaru($user);
        }
    }
}
