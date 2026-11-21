<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SoalRequest;
use App\Http\Resources\Admin\AdminSoalResource;
use App\Models\SoalKuis;
use App\Services\GambarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SoalAdminController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AdminSoalResource::collection(
            SoalKuis::with('biota:id,slug,nama')->latest('id')->paginate(10)
        );
    }

    public function store(SoalRequest $request, GambarService $gambar): JsonResponse
    {
        $data = $request->safe()->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'soal');
        }

        $soal = SoalKuis::create($data)->load('biota:id,slug,nama');

        return (new AdminSoalResource($soal))->response()->setStatusCode(201);
    }

    public function show(SoalKuis $soal): AdminSoalResource
    {
        return new AdminSoalResource($soal->load('biota:id,slug,nama'));
    }

    public function update(SoalRequest $request, SoalKuis $soal, GambarService $gambar): AdminSoalResource
    {
        $data = $request->safe()->except('gambar');
        $gambarLama = $soal->gambar;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $gambar->simpan($request->file('gambar'), 'soal');
        }

        $soal->update($data);

        if ($request->hasFile('gambar')) {
            $gambar->hapusBilaTidakDipakai($gambarLama, $soal);   // file lama dibersihkan setelah yang baru tersimpan
        }

        return new AdminSoalResource($soal->load('biota:id,slug,nama'));
    }

    public function destroy(SoalKuis $soal, GambarService $gambar): Response
    {
        $soal->delete();
        $gambar->hapusBilaTidakDipakai($soal->gambar, $soal);

        return response()->noContent();
    }
}
