<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KartuRequest;
use App\Http\Resources\Admin\AdminKartuResource;
use App\Models\Kartu;
use App\Services\GambarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class KartuAdminController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AdminKartuResource::collection(
            Kartu::with('biota:id,slug,nama')->orderBy('xp_syarat')->paginate(10)
        );
    }

    public function store(KartuRequest $request, GambarService $gambar): JsonResponse
    {
        $data = $request->safe()->except('gambar');
        $data['gambar'] = $gambar->simpan($request->file('gambar'), 'kartu');

        $kartu = Kartu::create($data)->load('biota:id,slug,nama');

        return (new AdminKartuResource($kartu))->response()->setStatusCode(201);
    }

    public function show(Kartu $kartu): AdminKartuResource
    {
        return new AdminKartuResource($kartu->load('biota:id,slug,nama'));
    }

    public function update(KartuRequest $request, Kartu $kartu, GambarService $gambar): AdminKartuResource
    {
        $data = $request->safe()->except('gambar');
        $gambarLama = $kartu->gambar;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'kartu');
        }

        $kartu->update($data);

        if ($request->hasFile('gambar')) {
            $gambar->hapusBilaTidakDipakai($gambarLama, $kartu);
        }

        return new AdminKartuResource($kartu->load('biota:id,slug,nama'));
    }

    public function destroy(Kartu $kartu, GambarService $gambar): Response
    {
        $kartu->delete();   // koleksi_kartu milik pemain ikut terhapus (cascade pada database)
        $gambar->hapusBilaTidakDipakai($kartu->gambar, $kartu);   // kartu seed memakai file yang sama dengan biota: tidak ikut terhapus

        return response()->noContent();
    }
}
