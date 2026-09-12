<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        return LearningMaterial::orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mapel'     => 'required|string|max:100',
            'judul'     => 'required|string|max:150',
            'kelas'     => 'required|string|max:50',
            'deskripsi' => 'required|string',
        ]);

        return LearningMaterial::create($data);
    }

    public function update(Request $request, LearningMaterial $learningMaterial)
    {
        $data = $request->validate([
            'mapel'     => 'required|string|max:100',
            'judul'     => 'required|string|max:150',
            'kelas'     => 'required|string|max:50',
            'deskripsi' => 'required|string',
        ]);

        $learningMaterial->update($data);

        return $learningMaterial;
    }

    public function destroy(LearningMaterial $learningMaterial)
    {
        $learningMaterial->delete();

        return response()->noContent();
    }
}