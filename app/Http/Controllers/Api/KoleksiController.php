<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KartuKoleksiResource;
use App\Models\Kartu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KoleksiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Satu query untuk id kartu yang dimiliki, satu untuk semua kartu (+ biota): tanpa N+1.
        $dimiliki = $user->koleksiKartu()->pluck('kartu_id')->all();

        $kartu = Kartu::with('biota:id,slug,nama')
            ->orderBy('xp_syarat')
            ->get()
            ->each(fn (Kartu $k) => $k->setAttribute('terbuka', in_array($k->id, $dimiliki)));

        return response()->json(['data' => [
            'total_xp' => $user->total_xp,
            'kartu' => KartuKoleksiResource::collection($kartu),
        ]]);
    }
}
