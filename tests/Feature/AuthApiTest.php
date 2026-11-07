<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('pengguna dapat mendaftar dan menerima token', function () {
    $res = $this->postJson('/api/v1/auth/register', [
        'name' => 'Budi Uji',
        'email' => 'budi@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $res->assertCreated()
        ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email', 'role', 'level', 'total_xp']]])
        ->assertJsonPath('data.user.role', 'user')
        ->assertJsonPath('data.user.total_xp', 0)
        ->assertJsonMissingPath('data.user.password');

    $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
});

test('pendaftar tidak dapat memilih role sendiri', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Nakal',
        'email' => 'nakal@example.com',
        'role' => 'admin',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertCreated()->assertJsonPath('data.user.role', 'user');
});

test('pendaftaran divalidasi', function (array $data, string $field) {
    $this->postJson('/api/v1/auth/register', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'nama kosong' => [['email' => 'a@b.com', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'], 'name'],
    'email tidak valid' => [['name' => 'A', 'email' => 'bukan-email', 'password' => 'rahasia123', 'password_confirmation' => 'rahasia123'], 'email'],
    'kata sandi pendek' => [['name' => 'A', 'email' => 'a@b.com', 'password' => '123', 'password_confirmation' => '123'], 'password'],
    'konfirmasi tidak cocok' => [['name' => 'A', 'email' => 'a@b.com', 'password' => 'rahasia123', 'password_confirmation' => 'lain'], 'password'],
]);

test('email yang sudah terdaftar ditolak', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'X',
        'email' => 'ada@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertJsonValidationErrors('email');
});

test('login berhasil memberi token yang dapat dipakai', function () {
    $user = User::factory()->create(['email' => 'demo@example.com']);   // kata sandi bawaan factory: password

    $token = $this->postJson('/api/v1/auth/login', ['email' => 'demo@example.com', 'password' => 'password'])
        ->assertOk()
        ->json('data.token');

    $this->withToken($token)->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'demo@example.com');
});

test('login gagal menghasilkan 422', function () {
    User::factory()->create(['email' => 'demo@example.com']);

    $this->postJson('/api/v1/auth/login', ['email' => 'demo@example.com', 'password' => 'salah'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('me tanpa token menghasilkan 401 berbentuk json', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized()->assertJsonStructure(['message']);
});

test('me tanpa header accept tetap menghasilkan 401 json, bukan redirect', function () {
    $this->get('/api/v1/auth/me')->assertUnauthorized();
});

test('logout menghapus token yang dipakai', function () {
    $user = User::factory()->create();
    $plain = $user->createToken('uji')->plainTextToken;

    $this->withToken($plain)->postJson('/api/v1/auth/logout')->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});
