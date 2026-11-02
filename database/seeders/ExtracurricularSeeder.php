<?php

namespace Database\Seeders;

use App\Models\Extracurricular;
use Illuminate\Database\Seeder;

class ExtracurricularSeeder extends Seeder
{
    /**
     * Selaraskan data ekstrakurikuler dengan folder foto di Google Drive.
     *
     * - Ekstrakurikuler yang punya folder Drive: dipasang banner WebP.
     * - Ekstrakurikuler yang belum ada: ditambahkan dengan data sementara.
     * - Ekstrakurikuler yang tidak ada di Drive: dihapus.
     *
     * Jalankan: php artisan db:seed --class=ExtracurricularSeeder
     */
    public function run(): void
    {
        $items = [
            ['judul' => 'Adiwiyata',        'slug' => 'adiwiyata',       'icon' => '🌱', 'kategori' => 'organisasi'],
            ['judul' => 'Atletik',          'slug' => 'atletik',         'icon' => '🏃', 'kategori' => 'olahraga'],
            ['judul' => 'B. Inggris',       'slug' => 'b-inggris',       'icon' => '📘', 'kategori' => 'organisasi'],
            ['judul' => 'Bulu Tangkis',     'slug' => 'bulu-tangkis',    'icon' => '🏸', 'kategori' => 'olahraga'],
            ['judul' => 'Futsal',           'slug' => 'futsal',          'icon' => '⚽', 'kategori' => 'olahraga'],
            ['judul' => 'IPA',              'slug' => 'ipa',             'icon' => '🔬', 'kategori' => 'organisasi'],
            ['judul' => 'Matematika',       'slug' => 'matematika',      'icon' => '📐', 'kategori' => 'organisasi'],
            ['judul' => 'Muai Thai',        'slug' => 'muai-thai',       'icon' => '🥊', 'kategori' => 'olahraga'],
            ['judul' => 'Pencak Silat',     'slug' => 'pencak-silat',    'icon' => '🥋', 'kategori' => 'olahraga'],
            ['judul' => 'PMR',              'slug' => 'pmr',             'icon' => '🩺', 'kategori' => 'organisasi'],
            ['judul' => 'Pustakawan Muda',  'slug' => 'pustakawan-muda', 'icon' => '📚', 'kategori' => 'organisasi'],
            ['judul' => 'Taekwondo',        'slug' => 'taekwondo',       'icon' => '🦵', 'kategori' => 'olahraga'],
            ['judul' => 'Takraw',           'slug' => 'takraw',          'icon' => '🤾', 'kategori' => 'olahraga'],
            ['judul' => 'Tari',             'slug' => 'tari',            'icon' => '💃', 'kategori' => 'seni'],
            ['judul' => 'Tenis Meja',       'slug' => 'tenis-meja',      'icon' => '🏓', 'kategori' => 'olahraga'],
            ['judul' => 'Vokal',            'slug' => 'vokal',           'icon' => '🎤', 'kategori' => 'seni'],
            ['judul' => 'Voli',             'slug' => 'voli',            'icon' => '🏐', 'kategori' => 'olahraga'],
            ['judul' => 'Wushu',            'slug' => 'wushu',           'icon' => '🥋', 'kategori' => 'olahraga'],
            ['judul' => 'OSIS',             'slug' => 'osis',            'icon' => '🏛️', 'kategori' => 'organisasi'],
            ['judul' => 'Pramuka',          'slug' => 'pramuka',         'icon' => '🏕️', 'kategori' => 'organisasi'],
        ];

        $keep = array_column($items, 'judul');

        $removed = Extracurricular::whereNotIn('judul', $keep)->delete();

        $created = 0;
        $updated = 0;

        foreach ($items as $item) {
            $gambar = 'images/ekstra/' . $item['slug'] . '.webp';
            $existing = Extracurricular::where('judul', $item['judul'])->first();

            if ($existing) {
                $existing->update(['gambar' => $gambar]);
                $updated++;
                continue;
            }

            Extracurricular::create([
                'icon'      => $item['icon'],
                'gambar'    => $gambar,
                'kategori'  => $item['kategori'],
                'judul'     => $item['judul'],
                'jadwal'    => 'Belum diatur',
                'lokasi'    => 'Belum diatur',
                'deskripsi' => 'Ekstrakurikuler ' . $item['judul'] . ' di MTsN 1 Wonosobo.',
            ]);
            $created++;
        }

        $this->command?->info("Ekstrakurikuler: {$created} ditambahkan, {$updated} banner diperbarui, {$removed} dihapus.");
    }
}
