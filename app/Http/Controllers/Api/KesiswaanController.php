<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesiswaanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KesiswaanController extends Controller
{
    public function index()
    {
        return KesiswaanItem::orderBy('urutan')->get()->map(function ($item) {
            $item->gambar_url = $item->gambar ? Storage::url($item->gambar) : null;
            return $item;
        });
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'urutan'    => 'nullable|integer',
            'gambar'    => 'nullable|image|max:2048',
        ]);

        $data['urutan'] = $data['urutan'] ?? (KesiswaanItem::max('urutan') + 1);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kesiswaan', 'public');
        }

        return KesiswaanItem::create($data);
    }

    public function update(Request $request, KesiswaanItem $kesiswaanItem)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'urutan'    => 'nullable|integer',
            'gambar'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($kesiswaanItem->gambar) {
                Storage::disk('public')->delete($kesiswaanItem->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('kesiswaan', 'public');
        }

        $kesiswaanItem->update($data);

        return $kesiswaanItem;
    }

    public function destroy(KesiswaanItem $kesiswaanItem)
    {
        if ($kesiswaanItem->gambar) {
            Storage::disk('public')->delete($kesiswaanItem->gambar);
        }

        $kesiswaanItem->delete();

        return response()->noContent();
    }
}