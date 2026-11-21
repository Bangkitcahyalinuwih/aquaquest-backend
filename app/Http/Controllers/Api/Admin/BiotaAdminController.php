<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BiotaRequest;
use App\Http\Resources\Admin\AdminBiotaResource;
use App\Models\Biota;
use App\Services\GambarService;
use App\Services\SlugService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BiotaAdminController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AdminBiotaResource::collection(Biota::orderBy('nama')->paginate(10));
    }

    /** Daftar ringkas (id dan nama) untuk pilihan di form edukasi, soal, dan kartu. */
    public function opsi(Request $request): JsonResponse
    {
        $data = Biota::query()
            ->when($request->boolean('tanpa_kartu'), fn ($query) => $query->doesntHave('kartu'))
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json(['data' => $data]);
    }

    public function store(BiotaRequest $request, GambarService $gambar, SlugService $slug): JsonResponse
    {
        $data = $request->safe()->except('gambar');
        $data['slug'] = $slug->unik(Biota::class, $data['nama']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'biota');
        }

        $biota = Biota::create($data);

        return (new AdminBiotaResource($biota))->response()->setStatusCode(201);
    }

    public function show(Biota $biota): AdminBiotaResource
    {
        return new AdminBiotaResource($biota);
    }

    public function update(BiotaRequest $request, Biota $biota, GambarService $gambar): AdminBiotaResource
    {
        $data = $request->safe()->except('gambar');
        $gambarLama = $biota->gambar;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'biota');
        }

        // Slug sengaja tidak diubah saat ubah data, supaya tautan yang sudah dibagikan tetap berlaku.
        $biota->update($data);

        if ($request->hasFile('gambar')) {
            $gambar->hapusBilaTidakDipakai($gambarLama, $biota);
        }

        return new AdminBiotaResource($biota);
    }

    public function destroy(Biota $biota, GambarService $gambar): Response
    {
        $kartu = $biota->kartu;   // diambil SEBELUM dihapus; kartunya ikut terhapus oleh cascade database

        $biota->delete();

        $gambar->hapusBilaTidakDipakai($biota->gambar, $biota);
        if ($kartu) {
            $gambar->hapusBilaTidakDipakai($kartu->gambar, $kartu);
        }

        return response()->noContent();
    }
}
