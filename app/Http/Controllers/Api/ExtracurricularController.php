<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Services\ImageStorage;
use App\Services\VercelBlob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class ExtracurricularController extends Controller
{
    /** Folder penyimpanan banner, relatif terhadap folder public. */
    const IMAGE_DIR = 'images/ekstra';

    /** Batas ukuran banner dalam kilobyte. */
    const IMAGE_MAX_KB = 4096;

    /** Format banner yang boleh diunggah. */
    const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp'];

    public function __construct(
        private readonly ImageStorage $images,
        private readonly VercelBlob $blob
    ) {
    }

    public function index()
    {
        return Extracurricular::orderBy('judul')->get()->map(function ($item) {
            $item->gambar_url = $this->gambarUrl($item->gambar);

            return $item;
        });
    }

    /**
     * Daftar banner yang sudah tersimpan, untuk dipilih dari panel admin
     * tanpa perlu mengunggah ulang.
     */
    public function images()
    {
        $rows = collect();

        $dir = public_path(self::IMAGE_DIR);

        if (File::isDirectory($dir)) {
            $rows = collect(File::files($dir))
                ->filter(function ($file) {
                    return in_array(strtolower($file->getExtension()), self::IMAGE_EXTENSIONS);
                })
                ->map(function ($file) {
                    $path = self::IMAGE_DIR . '/' . $file->getFilename();

                    return [
                        'nama' => $file->getFilename(),
                        'path' => $path,
                        'url'  => '/' . $path,
                    ];
                });
        }

        $rows = $rows->concat($this->blobImages());

        return $rows->unique('path')->sortBy('nama')->values();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $data['gambar'] = $request->hasFile('gambar')
            ? $this->simpanGambar($request)
            : $this->pathValid($request->input('gambar_pilihan'));

        unset($data['gambar_pilihan']);

        return Extracurricular::create($data);
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $data = $request->validate($this->rules());

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->simpanGambar($request, $request->input('judul', $extracurricular->judul));
        } elseif ($request->filled('gambar_pilihan')) {
            $data['gambar'] = $this->pathValid($request->input('gambar_pilihan'));
        } elseif ($request->boolean('hapus_gambar')) {
            $data['gambar'] = null;
        }

        unset($data['gambar_pilihan']);

        $extracurricular->update($data);

        return $extracurricular;
    }

    public function destroy(Extracurricular $extracurricular)
    {
        // Berkas banner tidak dihapus karena bisa dipakai
        // oleh ekstrakurikuler lain. Hanya referensinya yang dilepas.
        $extracurricular->delete();

        return response()->noContent();
    }

    private function rules(): array
    {
        return [
            'icon'            => 'required|string|max:10',
            'kategori'        => 'required|in:olahraga,seni,organisasi',
            'judul'           => 'required|string|max:100',
            'jadwal'          => 'required|string|max:100',
            'lokasi'          => 'required|string|max:150',
            'deskripsi'       => 'required|string',
            'gambar'          => 'nullable|image|mimes:' . implode(',', self::IMAGE_EXTENSIONS) . '|max:' . self::IMAGE_MAX_KB,
            'gambar_pilihan'  => 'nullable|string',
            'hapus_gambar'    => 'nullable|boolean',
        ];
    }

    /**
     * Simpan banner baru. Banner lama tetap dibiarkan agar bisa dipilih
     * kembali oleh ekstrakurikuler lain.
     */
    private function simpanGambar(Request $request, ?string $judul = null): ?string
    {
        if (! $request->hasFile('gambar')) {
            return $this->pathValid($request->input('gambar_pilihan'));
        }

        return $this->images->put(
            $request->file('gambar'),
            self::IMAGE_DIR,
            $judul ?: (string) $request->input('judul'),
            ImageStorage::PUBLIC_DIR
        );
    }

    /**
     * Pastikan path yang dikirim admin benar-benar berkas yang ada di
     * folder banner, supaya tidak bisa menunjuk ke luar folder.
     */
    private function pathValid(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = trim($path);

        $row = collect($this->images())->first(function ($row) use ($path) {
            return $row['path'] === $path || $row['url'] === $path;
        });

        if (! $row) {
            return null;
        }

        // Berkas di Vercel Blob disimpan sebagai URL absolut.
        return Str::startsWith($row['url'], ['http://', 'https://'])
            ? $row['url']
            : $row['path'];
    }

    /**
     * Daftar banner yang tersimpan di Vercel Blob (bila aktif).
     */
    private function blobImages()
    {
        if (! $this->images->usesBlob()) {
            return collect();
        }

        try {
            $blobs = $this->blob->list(self::IMAGE_DIR . '/');
        } catch (Throwable $e) {
            report($e);

            return collect();
        }

        return collect($blobs)
            ->filter(function ($blob) {
                $extension = strtolower(pathinfo($blob['pathname'] ?? '', PATHINFO_EXTENSION));

                return in_array($extension, self::IMAGE_EXTENSIONS);
            })
            ->map(function ($blob) {
                return [
                    'nama' => basename($blob['pathname']),
                    'path' => $blob['pathname'],
                    'url'  => $blob['url'],
                ];
            })
            ->values();
    }

    private function gambarUrl(?string $gambar): ?string
    {
        if (! $gambar) {
            return null;
        }

        if (Str::startsWith($gambar, ['http://', 'https://'])) {
            return $gambar;
        }

        return '/' . ltrim($gambar, '/');
    }
}
