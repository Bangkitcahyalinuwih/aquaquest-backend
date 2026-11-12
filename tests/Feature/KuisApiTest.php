<?php

use App\Models\GameHistory;
use App\Models\Kartu;
use App\Models\SoalKuis;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('alur kuis lengkap menambah xp dan tidak membocorkan kunci jawaban', function () {
    $user = User::factory()->create();
    SoalKuis::factory()->count(6)->create(['jawaban_benar' => 'a', 'poin' => 10]);
    Sanctum::actingAs($user);

    $mulai = $this->postJson('/api/v1/kuis/mulai');

    $mulai->assertOk()
        ->assertJsonStructure(['data' => [
            'game_id',
            'deadline_at',
            'soal' => ['*' => ['id', 'tipe', 'pertanyaan', 'gambar_url', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d']],
        ]])
        ->assertJsonCount(5, 'data.soal')
        ->assertJsonMissingPath('data.soal.0.jawaban_benar');

    $id = $mulai->json('data.game_id');

    foreach ($mulai->json('data.soal') as $soal) {
        $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $soal['id'], 'pilihan' => 'a'])
            ->assertOk()
            ->assertJsonPath('data.benar', true)
            ->assertJsonPath('data.jawaban_benar', 'a');
    }

    $this->postJson("/api/v1/kuis/{$id}/selesai")
        ->assertOk()
        ->assertJsonPath('data.jumlah_benar', 5)
        ->assertJsonPath('data.xp_didapat', 50)
        ->assertJsonPath('data.level', 1);

    expect($user->fresh()->total_xp)->toBe(50);
});

test('kartu terbuka otomatis saat xp mencapai syarat dan namanya dilaporkan', function () {
    $user = User::factory()->create();
    $kartu = Kartu::factory()->create(['xp_syarat' => 50, 'nama' => 'Kartu Uji']);
    SoalKuis::factory()->count(5)->create(['jawaban_benar' => 'a', 'poin' => 10]);
    Sanctum::actingAs($user);

    $mulai = $this->postJson('/api/v1/kuis/mulai');
    $id = $mulai->json('data.game_id');
    foreach ($mulai->json('data.soal') as $soal) {
        $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $soal['id'], 'pilihan' => 'a']);
    }

    $this->postJson("/api/v1/kuis/{$id}/selesai")
        ->assertOk()
        ->assertJsonPath('data.kartu_baru', ['Kartu Uji']);

    $this->assertDatabaseHas('koleksi_kartu', ['user_id' => $user->id, 'kartu_id' => $kartu->id]);
});

test('xp hanya bertambah pada sesi pertama setiap hari', function () {
    $user = User::factory()->create();
    SoalKuis::factory()->count(5)->create(['jawaban_benar' => 'a', 'poin' => 10]);
    Sanctum::actingAs($user);

    $main = function () {
        $mulai = $this->postJson('/api/v1/kuis/mulai');
        $id = $mulai->json('data.game_id');
        foreach ($mulai->json('data.soal') as $soal) {
            $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $soal['id'], 'pilihan' => 'a']);
        }

        return $this->postJson("/api/v1/kuis/{$id}/selesai")->json('data');
    };

    $pertama = $main();
    $kedua = $main();

    expect($pertama['xp_didapat'])->toBe(50)->and($pertama['sudah_main_hari_ini'])->toBeFalse();
    expect($kedua['xp_didapat'])->toBe(0)->and($kedua['sudah_main_hari_ini'])->toBeTrue();
    expect($user->fresh()->total_xp)->toBe(50);
});

test('jawaban setelah batas waktu ditolak dan sesi menjadi kedaluwarsa', function () {
    $user = User::factory()->create();
    $soal = SoalKuis::factory()->create(['jawaban_benar' => 'a']);
    $game = GameHistory::create([
        'user_id' => $user->id,
        'mulai_at' => now()->subMinutes(10),
        'deadline_at' => now()->subMinute(),
        'status' => 'berjalan',
        'soal_ids' => [$soal->id],
        'jawaban' => [],
    ]);
    Sanctum::actingAs($user);

    $this->postJson("/api/v1/kuis/{$game->id}/jawab", ['soal_id' => $soal->id, 'pilihan' => 'a'])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Waktu kuis sudah habis.');

    expect($game->fresh()->status)->toBe('kedaluwarsa');
});

test('soal di luar sesi dan jawaban ganda ditolak', function () {
    $user = User::factory()->create();
    SoalKuis::factory()->count(5)->create();
    $luar = SoalKuis::factory()->create();
    Sanctum::actingAs($user);

    $mulai = $this->postJson('/api/v1/kuis/mulai');
    $id = $mulai->json('data.game_id');
    $pertama = $mulai->json('data.soal.0.id');

    // soal yang tidak termasuk sesi
    $anggota = collect($mulai->json('data.soal'))->pluck('id');
    $bukanAnggota = SoalKuis::whereNotIn('id', $anggota)->first();
    $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $bukanAnggota->id, 'pilihan' => 'a'])->assertForbidden();

    // jawaban ganda
    $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $pertama, 'pilihan' => 'a'])->assertOk();
    $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $pertama, 'pilihan' => 'b'])->assertStatus(422);
});

test('pilihan selain a sampai d divalidasi', function () {
    $user = User::factory()->create();
    SoalKuis::factory()->count(5)->create();
    Sanctum::actingAs($user);

    $mulai = $this->postJson('/api/v1/kuis/mulai');

    $this->postJson('/api/v1/kuis/'.$mulai->json('data.game_id').'/jawab', ['soal_id' => $mulai->json('data.soal.0.id'), 'pilihan' => 'z'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('pilihan');
});

test('pemain tidak dapat memakai sesi milik pemain lain', function () {
    $pemilik = User::factory()->create();
    $penyusup = User::factory()->create();
    SoalKuis::factory()->count(5)->create();

    Sanctum::actingAs($pemilik);
    $mulai = $this->postJson('/api/v1/kuis/mulai');
    $id = $mulai->json('data.game_id');

    Sanctum::actingAs($penyusup);
    $this->postJson("/api/v1/kuis/{$id}/jawab", ['soal_id' => $mulai->json('data.soal.0.id'), 'pilihan' => 'a'])->assertForbidden();
    $this->postJson("/api/v1/kuis/{$id}/selesai")->assertForbidden();
});

test('menyelesaikan sesi dua kali ditolak dan xp tidak bertambah lagi', function () {
    $user = User::factory()->create();
    SoalKuis::factory()->count(5)->create(['jawaban_benar' => 'a']);
    Sanctum::actingAs($user);

    $id = $this->postJson('/api/v1/kuis/mulai')->json('data.game_id');
    $this->postJson("/api/v1/kuis/{$id}/selesai")->assertOk();
    $this->postJson("/api/v1/kuis/{$id}/selesai")->assertStatus(422);
});

test('kuis tidak dapat dimulai bila belum ada soal', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/kuis/mulai')
        ->assertStatus(422)
        ->assertJsonPath('message', 'Belum ada soal kuis yang tersedia.');
});

test('tamu tidak dapat memakai endpoint kuis', function () {
    $this->postJson('/api/v1/kuis/mulai')->assertUnauthorized();
});
