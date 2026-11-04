<?php

namespace App\Services;

use App\Exceptions\ImageUploadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Menyimpan gambar unggahan ke Vercel Blob bila tersedia, atau ke
 * penyimpanan lokal sebagai cadangan saat pengembangan.
 */
class ImageStorage
{
    /** Tujuan lokal: folder public aplikasi (public_path). */
    public const PUBLIC_DIR = 'public';

    /** Tujuan lokal: disk "public" Laravel (storage/app/public). */
    public const STORAGE_DISK = 'storage';

    public function __construct(private readonly VercelBlob $blob)
    {
    }

    public function usesBlob(): bool
    {
        return $this->blob->enabled();
    }

    /**
     * Simpan berkas unggahan.
     *
     * Nilai balikannya yang disimpan ke database: URL absolut saat memakai
     * Vercel Blob, atau path relatif saat memakai penyimpanan lokal.
     */
    public function put(
        UploadedFile $file,
        string $folder,
        string $namePrefix = 'gambar',
        string $localTarget = self::STORAGE_DISK
    ): string {
        $folder = trim($folder, '/');
        $filename = (Str::limit(Str::slug($namePrefix), 40, '') ?: 'gambar')
            . '-' . Str::lower(Str::random(8))
            . '.' . $this->extension($file);
        $pathname = $folder . '/' . $filename;

        if ($this->usesBlob()) {
            return $this->putToBlob($file, $pathname);
        }

        if ($localTarget === self::PUBLIC_DIR) {
            $dir = public_path($folder);

            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $file->move($dir, $filename);

            return $pathname;
        }

        return $file->storeAs($folder, $filename, 'public');
    }

    /**
     * Hapus gambar berdasarkan referensi yang tersimpan di database.
     */
    public function delete(?string $reference, string $localTarget = self::STORAGE_DISK): void
    {
        if (! $reference) {
            return;
        }

        if (Str::startsWith($reference, ['http://', 'https://'])) {
            if (! $this->usesBlob()) {
                return;
            }

            try {
                $this->blob->delete([$reference]);
            } catch (Throwable $e) {
                report($e);
            }

            return;
        }

        $path = ltrim($reference, '/');

        if ($localTarget === self::PUBLIC_DIR) {
            File::delete(public_path($path));

            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function putToBlob(UploadedFile $file, string $pathname): string
    {
        $realPath = $file->getRealPath();
        $contents = $realPath ? @file_get_contents($realPath) : false;

        if ($contents === false) {
            throw new ImageUploadException('Berkas gambar tidak bisa dibaca. Silakan unggah ulang.');
        }

        try {
            $result = $this->blob->put(
                $pathname,
                $contents,
                $file->getMimeType() ?: 'application/octet-stream'
            );
        } catch (Throwable $e) {
            report($e);

            throw new ImageUploadException('Gagal mengunggah gambar ke penyimpanan. Silakan coba lagi.');
        }

        $url = $result['url'] ?? $this->blob->url($pathname);

        if (! $url) {
            throw new ImageUploadException('Penyimpanan gambar tidak mengembalikan URL berkas.');
        }

        return $url;
    }

    private function extension(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');

        return $extension !== '' ? $extension : 'jpg';
    }
}
