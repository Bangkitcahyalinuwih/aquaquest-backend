<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('pengguna dapat mengubah nama', function () {
    $user = User::factory()->create(['name' => 'Lama']);
    Sanctum::actingAs($user);

    $this->putJson('/api/v1/profil', ['name' => 'Nama Baru'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Nama Baru');

    expect($user->fresh()->name)->toBe('Nama Baru');
});

test('mengganti kata sandi memerlukan kata sandi saat ini yang benar', function () {
    $user = User::factory()->create();   // kata sandi bawaan factory: password
    Sanctum::actingAs($user);

    $this->putJson('/api/v1/profil', [
        'name' => $user->name,
        'current_password' => 'salah',
        'password' => 'rahasiabaru1',
        'password_confirmation' => 'rahasiabaru1',
    ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

    $this->putJson('/api/v1/profil', [
        'name' => $user->name,
        'current_password' => 'password',
        'password' => 'rahasiabaru1',
        'password_confirmation' => 'rahasiabaru1',
    ])->assertOk();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'rahasiabaru1'])->assertOk();
});

test('kata sandi baru tanpa kata sandi lama atau tanpa konfirmasi ditolak', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/v1/profil', ['name' => 'X', 'password' => 'rahasiabaru1', 'password_confirmation' => 'rahasiabaru1'])
        ->assertJsonValidationErrors('current_password');

    $this->putJson('/api/v1/profil', ['name' => 'X', 'current_password' => 'password', 'password' => 'rahasiabaru1', 'password_confirmation' => 'beda'])
        ->assertJsonValidationErrors('password');
});

test('role dan xp tidak dapat diubah lewat profil', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson('/api/v1/profil', ['name' => 'X', 'role' => 'admin', 'total_xp' => 9999, 'level' => 99])->assertOk();

    $user->refresh();
    expect($user->role)->toBe('user')->and($user->total_xp)->toBe(0)->and($user->level)->toBe(1);
});

test('tamu tidak dapat mengubah profil', function () {
    $this->putJson('/api/v1/profil', ['name' => 'X'])->assertUnauthorized();
});
