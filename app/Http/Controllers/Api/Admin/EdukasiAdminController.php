<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EdukasiRequest;
use App\Http\Resources\Admin\AdminEdukasiResource;
use App\Models\KontenEdukasi;
use App\Services\GambarService;
use App\Services\SlugService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EdukasiAdminController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AdminEdukasiResource::collection(
            KontenEdukasi::with('biota:id,slug,nama')->latest('id')->paginate(10)
        );
    }

    public function store(EdukasiRequest $request, GambarService $gambar, SlugService $slug): JsonResponse
    {
        $data = $request->safe()->except('gambar');
        $data['slug'] = $slug->unik(KontenEdukasi::class, $data['judul']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'edukasi');
        }

        $konten = KontenEdukasi::create($data)->load('biota:id,slug,nama');

        return (new AdminEdukasiResource($konten))->response()->setStatusCode(201);
    }

    public function show(KontenEdukasi $konten): AdminEdukasiResource
    {
        return new AdminEdukasiResource($konten->load('biota:id,slug,nama'));
    }

    public function update(EdukasiRequest $request, KontenEdukasi $konten, GambarService $gambar): AdminEdukasiResource
    {
        $data = $request->safe()->except('gambar');
        $gambarLama = $konten->gambar;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'edukasi');
        }

        $konten->update($data);

        if ($request->hasFile('gambar')) {
            $gambar->hapusBilaTidakDipakai($gambarLama, $konten);
        }

        return new AdminEdukasiResource($konten->load('biota:id,slug,nama'));
    }

    public function destroy(KontenEdukasi $konten, GambarService $gambar): Response
    {
        $konten->delete();
        $gambar->hapusBilaTidakDipakai($konten->gambar, $konten);

        return response()->noContent();
    }
}
