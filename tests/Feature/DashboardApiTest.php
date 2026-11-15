<?php

use App\Models\GameHistory;
use App\Models\Kartu;
use App\Models\KoleksiKartu;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function pemainDenganXp(int $xp): User
{
    $user = User::factory()->create();
    User::whereKey($user->id)->update(['total_xp' => $xp, 'level' => intdiv($xp, 100) + 1]);

    return $user->fresh();
}

function sesiSelesai(User $user, $waktu): GameHistory
{
    return GameHistory::create([
        'user_id' => $user->id,
        'mulai_at' => $waktu,
        'deadline_at' => $waktu,
        'selesai_at' => $waktu,
        'status' => 'selesai',
        'soal_ids' => [],
        'jawaban' => [],
    ]);
}

test('dashboard berisi data pemain dan kartu berikutnya sesuai kontrak', function () {
    $user = pemainDenganXp(80);
    Kartu::factory()->create(['xp_syarat' => 50, 'nama' => 'Kartu Lama']);
    Kartu::factory()->create(['xp_syarat' => 100, 'nama' => 'Kartu Berikutnya']);
    Kartu::factory()->create(['xp_syarat' => 300, 'nama' => 'Kartu Jauh']);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/dashboard')
        ->assertOk()
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'level', 'total_xp'], 'kartu_berikutnya' => ['nama', 'xp_syarat'], 'jumlah_kartu_dimiliki', 'pernah_main', 'sudah_main_hari_ini']])
        ->assertJsonPath('data.user.total_xp', 80)
        ->assertJsonPath('data.kartu_berikutnya.nama', 'Kartu Berikutnya')
        ->assertJsonPath('data.kartu_berikutnya.xp_syarat', 100);
});

test('kartu berikutnya bernilai null bila semua kartu sudah terbuka', function () {
    $user = pemainDenganXp(500);
    Kartu::factory()->create(['xp_syarat' => 100]);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/dashboard')->assertOk()->assertJsonPath('data.kartu_berikutnya', null);
});

test('jumlah kartu dimiliki dihitung dari tabel koleksi milik pemain itu', function () {
    $user = pemainDenganXp(120);
    $lain = pemainDenganXp(120);
    $kartu = Kartu::factory()->create(['xp_syarat' => 100]);
    KoleksiKartu::create(['user_id' => $user->id, 'kartu_id' => $kartu->id, 'didapat_at' => now()]);
    KoleksiKartu::create(['user_id' => $lain->id, 'kartu_id' => $kartu->id, 'didapat_at' => now()]);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/dashboard')->assertJsonPath('data.jumlah_kartu_dimiliki', 1);
});

test('pemain baru belum pernah main', function () {
    Sanctum::actingAs(pemainDenganXp(0));

    $this->getJson('/api/v1/dashboard')
        ->assertOk()
        ->assertJsonPath('data.pernah_main', false)
        ->assertJsonPath('data.sudah_main_hari_ini', false);
});

test('sesi selesai hari ini menandai jatah xp harian terpakai', function () {
    $user = pemainDenganXp(0);
    sesiSelesai($user, now());
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/dashboard')
        ->assertOk()
        ->assertJsonPath('data.pernah_main', true)
        ->assertJsonPath('data.sudah_main_hari_ini', true);
});

test('sesi selesai kemarin tidak memakai jatah xp hari ini', function () {
    $user = pemainDenganXp(0);
    sesiSelesai($user, now()->subDay());
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/dashboard')
        ->assertJsonPath('data.pernah_main', true)
        ->assertJsonPath('data.sudah_main_hari_ini', false);
});

test('tamu tidak dapat membuka dashboard', function () {
    $this->getJson('/api/v1/dashboard')->assertUnauthorized();
});
