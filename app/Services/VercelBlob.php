<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien tipis untuk REST API Vercel Blob.
 *
 * Dipakai supaya gambar unggahan tetap tersimpan saat aplikasi berjalan di
 * Vercel (folder public bersifat read-only di sana). Bila token tidak diisi,
 * aplikasi otomatis memakai penyimpanan lokal seperti biasa.
 *
 * @see https://vercel.com/docs/vercel-blob
 */
class VercelBlob
{
    private const API_URL = 'https://vercel.com/api/blob';

    private const API_VERSION = '13';

    public function token(): ?string
    {
        $token = config('services.vercel_blob.token');

        return filled($token) ? trim((string) $token) : null;
    }

    public function enabled(): bool
    {
        return $this->token() !== null && $this->storeId() !== null;
    }

    /**
     * Store ID bisa diambil dari BLOB_STORE_ID atau dari token
     * (formatnya vercel_blob_rw_<storeId>_<secret>).
     */
    public function storeId(): ?string
    {
        $configured = config('services.vercel_blob.store_id');

        if (filled($configured)) {
            return trim((string) $configured);
        }

        $token = $this->token();

        if (! $token) {
            return null;
        }

        $parts = explode('_', $token);

        return filled($parts[3] ?? null) ? $parts[3] : null;
    }

    public function access(): string
    {
        return config('services.vercel_blob.access', 'public') === 'private' ? 'private' : 'public';
    }

    /**
     * URL publik sebuah blob berdasarkan pathname-nya.
     */
    public function url(string $pathname): ?string
    {
        $storeId = $this->storeId();

        if (! $storeId) {
            return null;
        }

        return sprintf(
            'https://%s.%s.blob.vercel-storage.com/%s',
            $storeId,
            $this->access(),
            ltrim($pathname, '/')
        );
    }

    /**
     * Unggah isi berkas. Mengembalikan respons API (berisi url & pathname).
     *
     * @return array{url?: string, pathname?: string, contentType?: string}
     */
    public function put(string $pathname, string $contents, string $contentType): array
    {
        $response = $this->request()
            ->withBody($contents, $contentType)
            ->withHeaders([
                'x-content-type' => $contentType,
                'x-add-random-suffix' => '0',
                'x-allow-overwrite' => '1',
                'x-vercel-blob-access' => $this->access(),
            ])
            ->put(self::API_URL . '/?pathname=' . rawurlencode($pathname));

        $this->ensureSuccessful($response, 'mengunggah');

        return (array) $response->json();
    }

    /**
     * Hapus satu atau beberapa blob berdasarkan URL-nya.
     *
     * @param  array<int, string>  $urls
     */
    public function delete(array $urls): void
    {
        $urls = array_values(array_filter($urls));

        if ($urls === []) {
            return;
        }

        $response = $this->request()
            ->asJson()
            ->post(self::API_URL . '/delete', ['urls' => $urls]);

        $this->ensureSuccessful($response, 'menghapus');
    }

    /**
     * Daftar blob pada sebuah prefix (folder).
     *
     * @return array<int, array{url: string, pathname: string, size?: int}>
     */
    public function list(string $prefix = '', int $limit = 1000): array
    {
        $blobs = [];
        $cursor = null;

        do {
            $query = array_filter([
                'prefix' => $prefix,
                'limit' => $limit,
                'cursor' => $cursor,
            ], fn ($value) => $value !== null && $value !== '');

            $response = $this->request()->get(self::API_URL . '/?' . http_build_query($query));

            $this->ensureSuccessful($response, 'membaca daftar');

            $payload = (array) $response->json();
            $blobs = array_merge($blobs, (array) ($payload['blobs'] ?? []));
            $cursor = $payload['hasMore'] ?? false ? ($payload['cursor'] ?? null) : null;
        } while ($cursor);

        return $blobs;
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        $token = $this->token();
        $storeId = $this->storeId();

        if (! $token || ! $storeId) {
            throw new RuntimeException('Vercel Blob belum dikonfigurasi.');
        }

        return Http::withToken($token)
            ->withHeaders([
                'x-vercel-blob-store-id' => $storeId,
                'x-api-version' => self::API_VERSION,
            ])
            ->acceptJson()
            ->timeout(60);
    }

    private function ensureSuccessful(Response $response, string $action): void
    {
        if ($response->successful()) {
            return;
        }

        $message = $response->json('error') ?: $response->body();

        throw new RuntimeException(sprintf(
            'Gagal %s gambar ke Vercel Blob (%d): %s',
            $action,
            $response->status(),
            $message
        ));
    }
}
