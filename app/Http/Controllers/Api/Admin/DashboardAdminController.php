<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Biota;
use App\Models\GameHistory;
use App\Models\Kartu;
use App\Models\KontenEdukasi;
use App\Models\SoalKuis;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            'jumlah_biota' => Biota::count(),
            'jumlah_edukasi' => KontenEdukasi::count(),
            'jumlah_soal' => SoalKuis::count(),
            'jumlah_kartu' => Kartu::count(),
            'jumlah_pemain' => User::where('role', 'user')->count(),
            'sesi_selesai' => GameHistory::where('status', 'selesai')->count(),

            'pemain_teratas' => User::where('role', 'user')
                ->orderByDesc('total_xp')->orderBy('id')->take(5)
                ->get(['id', 'name', 'level', 'total_xp']),

            // with('user:id,name'): satu query tambahan untuk semua nama pemain, bukan satu query per baris (N+1).
            'sesi_terbaru' => GameHistory::with('user:id,name')
                ->where('status', 'selesai')
                ->latest('selesai_at')->take(5)->get()
                ->map(fn (GameHistory $g) => [
                    'pemain' => $g->user->name,
                    'jumlah_benar' => $g->jumlah_benar,
                    'selesai_at' => $g->selesai_at->toIso8601String(),
                ]),
        ]]);
    }
}
