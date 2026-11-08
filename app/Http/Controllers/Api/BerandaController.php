<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BiotaListResource;
use App\Http\Resources\EdukasiListResource;
use App\Models\Biota;
use App\Models\KontenEdukasi;
use App\Models\SoalKuis;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class BerandaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => [
            'statistik' => [
                'jumlah_biota' => Biota::count(),
                'jumlah_soal' => SoalKuis::count(),
                'jumlah_pemain' => User::where('role', 'user')->count(),
            ],
            'biota_pilihan' => BiotaListResource::collection(Biota::inRandomOrder()->take(3)->get()),
            'edukasi_terbaru' => EdukasiListResource::collection(KontenEdukasi::latest()->take(3)->get()),
        ]]);
    }
}
