<?php

namespace Tests\Feature;

use App\Models\KesiswaanItem;
use App\Models\User;
use Database\Seeders\KesiswaanSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KesiswaanRoomTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role): User
    {
        return User::create([
            'name'     => 'Uji ' . $role . ' ' . uniqid(),
            'nis_nip'  => 'ruang-' . uniqid(),
            'role'     => $role,
            'password' => 'rahasia123',
        ]);
    }

    public function test_seeder_membuat_enam_ruang_dengan_ikon_dan_isi(): void
    {
        $this->seed(KesiswaanSeeder::class);

        $slugs = [
            'data-siswa',
            'kegiatan',
            'ekstrakurikuler',
            'beasiswa-pip',
            'kemitraan',
            'giat-lain',
        ];

        $rooms = KesiswaanItem::whereIn('slug', $slugs)->get()->keyBy('slug');

        $this->assertCount(6, $rooms);

        foreach ($slugs as $slug) {
            $this->assertNotNull($rooms[$slug]->ikon, "Ikon ruang {$slug} kosong.");
            $this->assertNotEmpty($rooms[$slug]->isi, "Isi ruang {$slug} kosong.");
        }
    }

    public function test_api_kesiswaan_mengirim_url_ikon_ruang(): void
    {
        $this->seed(KesiswaanSeeder::class);

        $response = $this->getJson('/api/kesiswaan')->assertOk();

        $rooms = collect($response->json())->keyBy('slug');

        $this->assertArrayHasKey('data-siswa', $rooms->all());
        $this->assertStringContainsString(
            'images/ruang/data-siswa.svg',
            $rooms['data-siswa']['ikon_url']
        );
    }

    public function test_admin_bisa_membuat_ruang_baru_dengan_slug_otomatis(): void
    {
        $admin = $this->makeUser('admin');
        $judul = 'Ruang Uji ' . uniqid();

        $response = $this->actingAs($admin)
            ->postJson('/api/kesiswaan', [
                'judul'        => $judul,
                'deskripsi'    => 'Deskripsi ruang uji.',
                'isi'          => "Paragraf pertama.\n\nParagraf kedua.",
                'ikon'         => 'kegiatan',
                'tautan'       => 'galeri',
                'tautan_label' => 'Lihat Galeri',
                'urutan'       => 99,
            ])
            ->assertCreated();

        $response->assertJsonPath('ikon', 'kegiatan');
        $response->assertJsonPath('tautan', 'galeri');
        $this->assertNotEmpty($response->json('slug'));
        $this->assertStringContainsString('images/ruang/kegiatan.svg', $response->json('ikon_url'));

        $this->assertDatabaseHas('kesiswaan_items', [
            'judul' => $judul,
            'ikon'  => 'kegiatan',
        ]);
    }

    public function test_ikon_di_luar_daftar_ditolak(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->postJson('/api/kesiswaan', [
                'judul'     => 'Ruang Ikon Salah',
                'deskripsi' => 'Deskripsi.',
                'ikon'      => 'ikon-tidak-ada',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ikon');
    }

    public function test_tautan_harus_halaman_portal_atau_url_lengkap(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->postJson('/api/kesiswaan', [
                'judul'     => 'Ruang Tautan Salah',
                'deskripsi' => 'Deskripsi.',
                'tautan'    => 'javascript:alert(1)',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('tautan');
    }

    public function test_pengunjung_tidak_bisa_membuat_ruang(): void
    {
        $this->postJson('/api/kesiswaan', [
            'judul'     => 'Ruang Publik',
            'deskripsi' => 'Deskripsi.',
        ])->assertUnauthorized();
    }
}
