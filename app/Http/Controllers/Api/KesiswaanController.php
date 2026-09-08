<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesiswaanItem;
use Illuminate\Http\Request;

class KesiswaanController extends Controller
{
    public function index()
    {
        return KesiswaanItem::orderBy('urutan')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'urutan'    => 'nullable|integer',
        ]);

        $data['urutan'] = $data['urutan'] ?? (KesiswaanItem::max('urutan') + 1);

        return KesiswaanItem::create($data);
    }

    public function update(Request $request, KesiswaanItem $kesiswaanItem)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'urutan'    => 'nullable|integer',
        ]);

        $kesiswaanItem->update($data);

        return $kesiswaanItem;
    }

    public function destroy(KesiswaanItem $kesiswaanItem)
    {
        $kesiswaanItem->delete();

        return response()->noContent();
    }
}