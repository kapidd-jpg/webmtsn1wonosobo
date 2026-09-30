<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtracurricularController extends Controller
{
    /** Folder penyimpanan banner, relatif terhadap folder public. */
    const IMAGE_DIR = 'images/ekstra';

    /** Batas ukuran banner dalam kilobyte. */
    const IMAGE_MAX_KB = 4096;

    /** Format banner yang boleh diunggah. */
    const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp'];

    public function index()
    {
        return Extracurricular::orderBy('judul')->get()->map(function ($item) {
            $item->gambar_url = $item->gambar ? '/' . ltrim($item->gambar, '/') : null;

            return $item;
        });
    }

    /**
     * Daftar banner yang sudah tersimpan, untuk dipilih dari panel admin
     * tanpa perlu mengunggah ulang.
     */
    public function images()
    {
        $dir = public_path(self::IMAGE_DIR);

        if (! File::isDirectory($dir)) {
            return [];
        }

        return collect(File::files($dir))
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
            })
            ->sortBy('nama')
            ->values();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $data['gambar'] = $this->simpanGambar($request, $request->input('judul'));
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
        // Berkas banner tidak dihapus dari folder karena bisa dipakai
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
     * Simpan banner baru ke folder publik. Bila tidak ada file baru,
     * path dari pilihan admin dipakai kembali.
     */
    private function simpanGambar(Request $request, string $judul): ?string
    {
        $dir = public_path(self::IMAGE_DIR);

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $ext = strtolower($file->getClientOriginalExtension());
            $nama = (Str::slug($judul) ?: 'ekstra') . '-' . Str::lower(Str::random(6)) . '.' . $ext;

            $file->move($dir, $nama);

            return self::IMAGE_DIR . '/' . $nama;
        }

        return $this->pathValid($request->input('gambar_pilihan'));
    }

    /**
     * Pastikan path yang dikirim admin benar-benar file yang ada di
     * folder banner, supaya tidak bisa menunjuk ke luar folder.
     */
    private function pathValid(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $ada = collect($this->images())->contains(function ($row) use ($path) {
            return $row['path'] === $path;
        });

        return $ada ? $path : null;
    }
}