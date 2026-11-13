<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfilController extends Controller
{
    public function update(ProfilRequest $request): UserResource
    {
        $user = $request->user();
        $data = $request->validated();

        if (! empty($data['password'])) {
            // Bukti bahwa yang mengganti kata sandi memang pemilik akun (bukan sekadar pemegang token yang tercuri).
            if (! Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages(['current_password' => ['Kata sandi saat ini salah.']]);
            }

            $user->password = $data['password'];   // di-hash otomatis oleh cast 'hashed' pada model
        }

        // Hanya nama yang boleh diubah dari sini. role, total_xp, dan level tidak pernah menerima input pengguna.
        $user->name = $data['name'];
        $user->save();

        return new UserResource($user);
    }
}
