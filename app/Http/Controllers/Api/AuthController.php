<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MASA_BERLAKU_HARI = 7;

    public function register(RegisterRequest $request): JsonResponse
    {
        // Kata sandi di-hash otomatis oleh cast 'hashed' pada model User (bawaan Laravel 12).
        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        $user->refresh();   // memuat nilai bawaan database: role=user, total_xp=0, level=1

        return $this->responToken($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            // 422 (bukan 401) supaya frontend menampilkannya sebagai galat kolom form.
            // Pesan sengaja sama untuk email salah dan kata sandi salah agar tidak membocorkan email mana yang terdaftar.
            throw ValidationException::withMessages(['email' => ['Email atau kata sandi salah.']]);
        }

        return $this->responToken($user, 200);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        // Hanya token yang sedang dipakai yang dihapus (perangkat lain tetap login).
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();   // 204
    }

    private function responToken(User $user, int $status): JsonResponse
    {
        $token = $user->createToken('spa', ['*'], now()->addDays(self::MASA_BERLAKU_HARI))->plainTextToken;

        return response()->json([
            'data' => ['token' => $token, 'user' => new UserResource($user)],
        ], $status);
    }
}
