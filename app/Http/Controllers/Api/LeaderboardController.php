<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function index(): JsonResponse
    {
        $pemain = User::query()
            ->where('role', 'user')            // akun admin tidak ikut peringkat
            ->orderByDesc('total_xp')
            ->orderBy('id')                    // urutan sekunder: hasil selalu sama bila XP kembar
            ->take(20)
            ->get(['id', 'name', 'level', 'total_xp']);   // hanya kolom yang perlu (email tidak ikut keluar)

        return response()->json([
            'data' => $pemain->values()->map(fn (User $u, int $i) => [
                'peringkat' => $i + 1,
                'id' => $u->id,
                'name' => $u->name,
                'level' => $u->level,
                'total_xp' => $u->total_xp,
            ]),
        ]);
    }
}
