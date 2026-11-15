<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\GameHistory;
use App\Models\Kartu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Kartu terdekat yang BELUM terbuka: syarat XP-nya paling kecil di atas XP pemain.
        $berikutnya = Kartu::query()
            ->where('xp_syarat', '>', $user->total_xp)
            ->orderBy('xp_syarat')
            ->first(['nama', 'xp_syarat']);

        return response()->json(['data' => [
            'user' => new UserResource($user),
            'kartu_berikutnya' => $berikutnya
                ? ['nama' => $berikutnya->nama, 'xp_syarat' => $berikutnya->xp_syarat]
                : null,
            'jumlah_kartu_dimiliki' => $user->koleksiKartu()->count(),
            'pernah_main' => $user->gameHistory()->where('status', 'selesai')->exists(),
            // Memakai method yang SAMA dengan KuisService, sehingga angka di dashboard tidak mungkin berbeda dari perilaku kuis.
            'sudah_main_hari_ini' => GameHistory::sudahSelesaiHariIni($user->id),
        ]]);
    }
}
