<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use Illuminate\Http\Request;

class ExtracurricularController extends Controller
{
    public function index()
    {
        return Extracurricular::orderBy('judul')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon'      => 'required|string|max:10',
            'kategori'  => 'required|in:olahraga,seni,organisasi',
            'judul'     => 'required|string|max:100',
            'jadwal'    => 'required|string|max:100',
            'lokasi'    => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        return Extracurricular::create($data);
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $data = $request->validate([
            'icon'      => 'required|string|max:10',
            'kategori'  => 'required|in:olahraga,seni,organisasi',
            'judul'     => 'required|string|max:100',
            'jadwal'    => 'required|string|max:100',
            'lokasi'    => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        $extracurricular->update($data);

        return $extracurricular;
    }

    public function destroy(Extracurricular $extracurricular)
    {
        $extracurricular->delete();

        return response()->noContent();
    }
}