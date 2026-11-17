<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Extracurricular;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SchoolProfile;

class StelaController extends Controller
{
    public function reply(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|in:user,model',
            'history.*.text' => 'required_with:history|string',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);
        $context = $this->buildContext();

        $reply = $this->askGemini($userMessage, $history, $context);

        return response()->json(['reply' => $reply]);
    }

       private function buildContext(): string
    {
        $profile = SchoolProfile::first();

        $profilText = $profile ? <<<TEXT
        Nama Sekolah: {$profile->nama_sekolah}
        Kepala Sekolah: {$profile->kepala_sekolah}
        Alamat: {$profile->alamat}
        Telepon: {$profile->telepon}
        Email: {$profile->email}
        Tahun Berdiri: {$profile->tahun_berdiri}
        Akreditasi: {$profile->akreditasi}
        Visi: {$profile->visi}
        Misi: {$profile->misi}
        Sejarah Singkat: {$profile->sejarah_singkat}
        TEXT : 'Belum ada data profil sekolah.';

        $schedules = Schedule::all()->map(function ($s) {
            return "{$s->hari}, {$s->jam}: {$s->mapel} - {$s->kelas} ({$s->guru})";
        })->implode("\n");

        $extras = Extracurricular::all()->map(function ($x) {
            return "{$x->judul} ({$x->kategori}) - {$x->jadwal} di {$x->lokasi}: {$x->deskripsi}";
        })->implode("\n");

        $announcements = Announcement::orderByDesc('id')->limit(5)->get()->map(function ($a) {
            return "[{$a->tanggal} {$a->bulan}] {$a->judul}: {$a->deskripsi}";
        })->implode("\n");

        return <<<TEXT
        PROFIL SEKOLAH:
        {$profilText}

        DATA JADWAL PELAJARAN:
        {$schedules}

        DATA EKSTRAKURIKULER:
        {$extras}

        PENGUMUMAN TERBARU:
        {$announcements}
        TEXT;
    }

        private function systemPrompt(string $context): string
    {
        return <<<TEXT
        Kamu adalah STELA, asisten AI untuk Portal Kesiswaan MTsN 1 Wonosobo.
        Pengguna kamu adalah siswa madrasah (remaja) dan guru.

        KEMAMPUAN UMUM:
        - Kamu boleh menjawab pertanyaan apa saja dengan ramah dan membantu:
          pelajaran sekolah, pengetahuan umum, atau obrolan santai.
        - Jika pertanyaan berkaitan dengan portal ini (jadwal pelajaran,
          ekstrakurikuler, pengumuman, cara login), UTAMAKAN jawaban dari
          DATA PORTAL di bawah — jangan mengarang informasi yang bertentangan
          dengan data itu.
        - Untuk pertanyaan di luar data portal, jawab menggunakan pengetahuan
          umum kamu sebisa mungkin, secara singkat dan jelas.
        - Karena penggunamu kebanyakan siswa madrasah (remaja), jaga jawaban
          tetap sopan, positif, dan sesuai untuk usia sekolah. Hindari konten
          yang tidak pantas untuk anak sekolah.

        ATURAN KHUSUS SAAT SISWA BERCERITA MASALAH / MAU KONSELING:
        - Jika siswa terlihat ingin curhat, bingung cara mengungkapkan masalahnya,
          atau menyebut masalah pribadi (pertemanan, keluarga, belajar, perasaan),
          dengarkan dengan hangat dan penuh empati. Bantu dia merangkai kata-kata
          untuk menjelaskan apa yang dia rasakan/alami.
        - JANGAN berpura-pura menjadi konselor/psikolog. Kamu bukan pengganti
          guru BK. Jangan memberi diagnosis, nasihat psikologis mendalam, atau
          menyimpulkan penyebab masalah siswa.
        - Setelah membantu siswa merumuskan ceritanya, SELALU arahkan dia untuk
          melanjutkan ke form Konseling di halaman ini, atau bicara langsung
          dengan guru BK di sekolah.
        - Jika siswa menunjukkan tanda-tanda krisis serius (menyebut ingin
          menyakiti diri sendiri, putus asa berat, atau sejenisnya), JANGAN
          mencoba menangani sendiri. Sampaikan dengan lembut bahwa perasaan itu
          penting dan dia tidak sendirian, lalu SEGERA dorong dia untuk bicara
          ke guru BK, wali kelas, atau orang dewasa tepercaya SEKARANG, dan
          sebutkan bahwa dia bisa juga menghubungi layanan konseling/hotline
          kesehatan jiwa jika butuh bantuan segera.
        - Jangan pernah membuat siswa merasa masalahnya remeh atau diabaikan.

        GAYA BICARA:
        - Ramah, hangat, singkat, dan pakai Bahasa Indonesia yang mudah dipahami
          remaja. Hindari jawaban panjang bertele-tele.

        DATA PORTAL YANG BISA KAMU RUJUK:
        {$context}
        TEXT;
    }

    private function askGemini(string $userMessage, array $history, string $context): string
    {
        $apiKey = config('services.gemini.key');

        if (! $apiKey) {
            return 'Maaf, STELA belum dikonfigurasi dengan benar. Hubungi admin portal.';
        }

        // Susun riwayat percakapan (maks 10 pesan terakhir supaya tidak kebesaran)
        $contents = [];
        $trimmedHistory = array_slice($history, -10);

        foreach ($trimmedHistory as $item) {
            $contents[] = [
                'role' => $item['role'] === 'model' ? 'model' : 'user',
                'parts' => [['text' => $item['text']]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        // Model utama dua kali, lalu model cadangan. Gemini sering balas 503
        // "high demand" secara sesaat, jadi satu percobaan saja tidak cukup.
        $models = collect([
            config('services.gemini.model', 'gemini-3.1-flash-lite'),
            ...config('services.gemini.fallback_models', []),
        ])->filter()->unique()->values()->all();

        $maxOutputTokens = (int) config('services.gemini.max_output_tokens', 2048);
        $generationConfig = [
            'maxOutputTokens' => $maxOutputTokens > 0 ? $maxOutputTokens : 2048,
        ];

        $thinkingLevel = config('services.gemini.thinking_level');
        if ($thinkingLevel) {
            $generationConfig['thinkingConfig'] = ['thinkingLevel' => $thinkingLevel];
        }

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->systemPrompt($context)]],
            ],
            'contents' => $contents,
            'generationConfig' => $generationConfig,
        ];

        // Function Vercel punya batas waktu, jadi seluruh percobaan dibatasi
        // oleh deadline bersama (bukan timeout tiap request saja).
        $deadline = microtime(true) + 20.0;
        $lastStatus = null;
        $lastBody = '';
        $sawCongestion = false;
        $sawQuota = false;
        $sawTimeout = false;

        foreach ($models as $index => $model) {
            $maxAttempts = $index === 0 ? 2 : 1;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                $remaining = $deadline - microtime(true);
                if ($remaining < 1.5) {
                    break 2;
                }

                $attemptStarted = microtime(true);

                try {
                    $response = Http::timeout((int) max(2, min(9, ceil($remaining))))->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/'
                            . rawurlencode($model)
                            . ':generateContent?key=' . $apiKey,
                        $payload
                    );

                    if ($response->successful()) {
                        $data = $response->json();
                        $candidate = $data['candidates'][0] ?? [];
                        $finishReason = $candidate['finishReason'] ?? null;

                        // Gemini bisa memecah jawaban menjadi beberapa "part";
                        // kalau hanya part pertama yang diambil, balasan tampak terpotong.
                        $text = collect($candidate['content']['parts'] ?? [])
                            ->pluck('text')
                            ->filter()
                            ->implode('');

                        if ($finishReason === 'MAX_TOKENS') {
                            Log::warning('STELA: jawaban Gemini mencapai batas token', [
                                'model' => $model,
                                'max_output_tokens' => $maxOutputTokens,
                                'chars' => mb_strlen($text),
                            ]);
                        }

                        return $text !== '' ? $text : 'Maaf, aku belum punya jawaban untuk itu.';
                    }

                    $lastStatus = $response->status();
                    $lastBody = mb_substr($response->body(), 0, 300);

                    // Model sudah dihapus/diganti (404): jangan berhenti di sini,
                    // langsung coba model cadangan berikutnya.
                    if ($lastStatus === 404) {
                        Log::warning('STELA: model Gemini tidak tersedia', [
                            'model' => $model,
                            'body' => $lastBody,
                        ]);
                        continue 2;
                    }

                    // 503/429 dan 5xx bersifat sementara: coba lagi sebentar,
                    // lalu lanjut ke model cadangan bila masih gagal.
                    if (in_array($lastStatus, [408, 429, 500, 502, 503, 504], true)) {
                        $sawCongestion = $sawCongestion || $lastStatus === 503;
                        $sawQuota = $sawQuota || $lastStatus === 429;

                        Log::warning('STELA: Gemini belum bisa melayani', [
                            'model' => $model,
                            'attempt' => $attempt,
                            'status' => $lastStatus,
                            'body' => $lastBody,
                        ]);

                        $fastFailure = (microtime(true) - $attemptStarted) < 2.0;
                        if ($attempt < $maxAttempts && $fastFailure) {
                            usleep(350000);
                            continue;
                        }

                        continue 2;
                    }

                    Log::error('Gemini API error', [
                        'model' => $model,
                        'status' => $lastStatus,
                        'body' => $lastBody,
                    ]);

                    return 'Maaf, STELA sedang mengalami gangguan. Coba lagi sebentar lagi, ya.';
                } catch (\Exception $e) {
                    // Pesan exception Guzzle memuat URL lengkap, termasuk ?key=...
                    // Jangan pernah menuliskan API key ke log.
                    $safeError = preg_replace('/(key=)[^&\s]+/i', '$1***', $e->getMessage());

                    Log::error('Gemini request failed', [
                        'model' => $model,
                        'error' => $safeError,
                    ]);

                    $sawTimeout = $sawTimeout || $e instanceof \Illuminate\Http\Client\ConnectionException
                        || str_contains($e->getMessage(), 'timed out')
                        || str_contains($e->getMessage(), 'cURL error 28');

                    // Request gagal di tingkat koneksi: jangan buang waktu
                    // mencoba model yang sama, langsung ke model cadangan.
                    $lastStatus = null;
                    continue 2;
                }
            }
        }

        Log::error('STELA: semua model Gemini gagal', [
            'status' => $lastStatus,
            'body' => $lastBody,
        ]);

        if ($sawQuota) {
            return 'Maaf, kuota STELA sedang penuh. Coba lagi nanti, ya.';
        }

        if ($sawCongestion) {
            return 'Maaf, STELA lagi agak sibuk. Coba lagi sebentar, ya.';
        }

        if ($sawTimeout) {
            return 'Maaf, STELA sedang lambat merespons. Coba lagi sebentar, ya.';
        }

        return 'Maaf, STELA sedang tidak dapat diakses. Coba lagi sebentar lagi, ya.';
    }
}