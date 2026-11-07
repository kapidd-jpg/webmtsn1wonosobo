<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesiswaanItem;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KesiswaanController extends Controller
{
    public const IKONS = [
        'data-siswa',
        'kegiatan',
        'ekstrakurikuler',
        'beasiswa-pip',
        'kemitraan',
        'giat-lain',
    ];

    public const TAUTAN_INTERNAL = [
        'home',
        'kesiswaan',
        'ekstra',
        'galeri',
        'pengumuman',
        'konseling',
        'profil',
        'guru-dashboard',
        'login',
    ];

    public function __construct(private readonly ImageStorage $images)
    {
    }

    public function index()
    {
        return KesiswaanItem::orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->map(fn (KesiswaanItem $item) => $this->decorate($item));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $data['urutan'] = $data['urutan'] ?? ((int) KesiswaanItem::max('urutan') + 1);
        $data['slug'] = $this->uniqueSlug($data['judul']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->simpanGambar($request, $data['judul']);
        }

        $item = KesiswaanItem::create($data);

        return response()->json($this->decorate($item), 201);
    }

    public function update(Request $request, KesiswaanItem $kesiswaanItem)
    {
        $data = $this->validatePayload($request);

        if (blank($kesiswaanItem->slug)) {
            $data['slug'] = $this->uniqueSlug($data['judul'], $kesiswaanItem->id);
        }

        if ($request->hasFile('gambar')) {
            $this->images->delete($kesiswaanItem->gambar);
            $data['gambar'] = $this->simpanGambar($request, $data['judul']);
        }

        $kesiswaanItem->update($data);

        return $this->decorate($kesiswaanItem->fresh());
    }

    public function destroy(KesiswaanItem $kesiswaanItem)
    {
        $this->images->delete($kesiswaanItem->gambar);

        $kesiswaanItem->delete();

        return response()->noContent();
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'judul'        => ['required', 'string', 'max:150'],
            'deskripsi'    => ['required', 'string'],
            'isi'          => ['nullable', 'string'],
            'ikon'         => ['nullable', Rule::in(self::IKONS)],
            'tautan'       => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (blank($value)) {
                    return;
                }

                $isInternal = in_array($value, self::TAUTAN_INTERNAL, true);
                $isUrl = Str::startsWith($value, ['http://', 'https://']);

                if (! $isInternal && ! $isUrl) {
                    $fail('Tautan harus berupa halaman portal atau alamat lengkap yang diawali http:// atau https://.');
                }
            }],
            'tautan_label' => ['nullable', 'string', 'max:60'],
            'urutan'       => ['nullable', 'integer'],
            'gambar'       => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function uniqueSlug(string $judul, ?int $ignoreId = null): string
    {
        $base = Str::slug($judul) ?: 'ruang';
        $slug = $base;
        $counter = 2;

        while (
            KesiswaanItem::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function decorate(KesiswaanItem $item): KesiswaanItem
    {
        $item->gambar_url = $this->gambarUrl($item->gambar);
        $item->ikon_url = $this->ikonUrl($item->ikon);

        return $item;
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

    private function ikonUrl(?string $ikon): string
    {
        $ikon = in_array($ikon, self::IKONS, true) ? $ikon : 'default';

        return asset('images/ruang/' . $ikon . '.svg');
    }
}
