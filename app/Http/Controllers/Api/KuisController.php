<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JawabKuisRequest;
use App\Http\Resources\SoalKuisSesiResource;
use App\Models\GameHistory;
use App\Services\KuisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    public function mulai(Request $request, KuisService $kuis): JsonResponse
    {
        $game = $kuis->mulai($request->user());   // 422 bila belum ada soal (abort di service)

        return response()->json(['data' => [
            'game_id' => $game->id,
            'deadline_at' => $game->deadline_at->toIso8601String(),
            'soal' => SoalKuisSesiResource::collection($kuis->soalUntukSesi($game)),
        ]]);
    }

    public function jawab(JawabKuisRequest $request, GameHistory $game, KuisService $kuis): JsonResponse
    {
        $this->pastikanPemilik($request, $game);

        // Seluruh validasi keamanan (status, batas waktu, soal anggota sesi, jawaban ganda) ada di KuisService::jawab().
        return response()->json(['data' => $kuis->jawab($game, $request->validated('soal_id'), $request->validated('pilihan'))]);
    }

    public function selesai(Request $request, GameHistory $game, KuisService $kuis): JsonResponse
    {
        $this->pastikanPemilik($request, $game);

        return response()->json(['data' => $kuis->selesai($game)]);
    }

    /** Mencegah pemain A memakai game_id milik pemain B (misalnya dengan menebak angka di URL). */
    private function pastikanPemilik(Request $request, GameHistory $game): void
    {
        abort_if($game->user_id !== $request->user()->id, 403, 'Sesi ini bukan milikmu.');
    }
}
