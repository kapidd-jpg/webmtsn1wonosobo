<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesiswaanItem;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KesiswaanController extends Controller
{
    public function __construct(private readonly ImageStorage $images)
    {
    }

    public function index()
    {
        return KesiswaanItem::orderBy('urutan')->get()->map(function ($item) {
            $item->gambar_url = $this->gambarUrl($item->gambar);
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
            $data['gambar'] = $this->simpanGambar($request, $data['judul']);
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
            $this->images->delete($kesiswaanItem->gambar);
            $data['gambar'] = $this->simpanGambar($request, $data['judul']);
        }

        $kesiswaanItem->update($data);

        return $kesiswaanItem;
    }

    public function destroy(KesiswaanItem $kesiswaanItem)
    {
        $this->images->delete($kesiswaanItem->gambar);

        $kesiswaanItem->delete();

        return response()->noContent();
    }

    private function simpanGambar(Request $request, ?string $judul = null): ?string
    {
        return $this->images->put(
            $request->file('gambar'),
            'kesiswaan',
            $judul ?: 'kesiswaan',
            ImageStorage::STORAGE_DISK
        );
    }

    private function gambarUrl(?string $gambar): ?string
    {
        if (! $gambar) {
            return null;
        }

        if (Str::startsWith($gambar, ['http://', 'https://'])) {
            return $gambar;
        }

        return Storage::url($gambar);
    }
}
