<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EdukasiListResource;
use App\Http\Resources\EdukasiResource;
use App\Models\KontenEdukasi;
use Illuminate\Http\Request;

class EdukasiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $konten = KontenEdukasi::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $kata = '%'.$request->q.'%';
                $query->where(fn ($w) => $w->where('judul', 'like', $kata)->orWhere('isi', 'like', $kata));
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return EdukasiListResource::collection($konten);
    }

    public function show(KontenEdukasi $konten): EdukasiResource
    {
        return new EdukasiResource($konten->load('biota:id,slug,nama'));
    }
}
