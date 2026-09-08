<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Extracurricular;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        try {
            $response = Http::timeout(15)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $apiKey,
                [
                    'system_instruction' => [
                        'parts' => [['text' => $this->systemPrompt($context)]],
                    ],
                    'contents' => $contents,
                ]
            );

            if (! $response->successful()) {
                Log::error('Gemini API error', ['body' => $response->body()]);
                return 'Maaf, STELA sedang mengalami gangguan. Coba lagi sebentar lagi, ya.';
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            return $text ?: 'Maaf, aku belum punya jawaban untuk itu.';
        } catch (\Exception $e) {
            Log::error('Gemini request failed', ['error' => $e->getMessage()]);
            return 'Maaf, STELA sedang tidak dapat diakses. Coba lagi sebentar lagi, ya.';
        }
    }
}