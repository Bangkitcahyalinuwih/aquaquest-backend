<?php

use App\Models\User;

test('leaderboard terurut menurut xp dan memberi nomor peringkat', function () {
    $c = User::factory()->create(['name' => 'C']);
    $a = User::factory()->create(['name' => 'A']);
    $b = User::factory()->create(['name' => 'B']);
    // total_xp dan level tidak ada di $fillable, jadi diisi lewat query langsung
    User::whereKey($a->id)->update(['total_xp' => 300, 'level' => 4]);
    User::whereKey($b->id)->update(['total_xp' => 120, 'level' => 2]);

    $this->getJson('/api/v1/leaderboard')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.name', 'A')
        ->assertJsonPath('data.0.peringkat', 1)
        ->assertJsonPath('data.1.name', 'B')
        ->assertJsonPath('data.2.name', 'C')
        ->assertJsonPath('data.2.peringkat', 3)
        ->assertJsonStructure(['data' => ['*' => ['peringkat', 'id', 'name', 'level', 'total_xp']]]);
});

test('admin tidak ikut peringkat dan email tidak bocor', function () {
    User::factory()->create(['role' => 'admin', 'name' => 'Admin']);
    User::factory()->create(['name' => 'Pemain']);

    $this->getJson('/api/v1/leaderboard')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonMissingPath('data.0.email')
        ->assertJsonPath('data.0.name', 'Pemain');
});

test('leaderboard hanya menampilkan 20 teratas dan dapat diakses tamu', function () {
    User::factory()->count(25)->create();

    $this->getJson('/api/v1/leaderboard')->assertOk()->assertJsonCount(20, 'data');
});
