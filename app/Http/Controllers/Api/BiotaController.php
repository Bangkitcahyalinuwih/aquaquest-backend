<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BiotaListResource;
use App\Http\Resources\BiotaResource;
use App\Models\Biota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiotaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $biota = Biota::query()
            // when(): filter hanya dipasang jika parameternya diisi
            ->when($request->filled('q'), function ($query) use ($request) {
                $kata = '%'.$request->q.'%';
                $query->where(fn ($w) => $w->where('nama', 'like', $kata)->orWhere('nama_latin', 'like', $kata));
            })
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori', $request->kategori))
            ->orderBy('nama')
            ->paginate(9)
            ->withQueryString();   // tautan halaman berikutnya mempertahankan q dan kategori

        // collection() otomatis menghasilkan { data, links, meta } untuk hasil paginate
        return BiotaListResource::collection($biota);
    }

    public function kategori(): JsonResponse
    {
        return response()->json([
            'data' => Biota::query()->distinct()->orderBy('kategori')->pluck('kategori'),
        ]);
    }

    public function show(Biota $biota): BiotaResource
    {
        return new BiotaResource($biota);
    }
}
