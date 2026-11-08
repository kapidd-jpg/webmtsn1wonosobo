<?php

namespace Tests\Feature;

use App\Models\KesiswaanItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GuruContentPermissionTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role): User
    {
        return User::create([
            'name'     => 'Uji ' . $role . ' ' . uniqid(),
            'nis_nip'  => 'guru-' . uniqid(),
            'role'     => $role,
            'password' => 'rahasia123',
        ]);
    }

    private function makeRoom(): KesiswaanItem
    {
        return KesiswaanItem::create([
            'judul'     => 'Ruang Uji ' . uniqid(),
            'slug'      => 'ruang-uji-' . uniqid(),
            'deskripsi' => 'Deskripsi ruang uji.',
            'urutan'    => 90,
        ]);
    }

    private function announcementPayload(): array
    {
        return [
            'tanggal'   => '7',
            'bulan'     => 'Nov',
            'kategori'  => 'Umum',
            'judul'     => 'Pengumuman Uji ' . uniqid(),
            'deskripsi' => 'Isi pengumuman uji.',
        ];
    }

    public function test_guru_bisa_membuat_dan_mengubah_ruang(): void
    {
        $guru = $this->makeUser('guru');

        $response = $this->actingAs($guru)
            ->postJson('/api/kesiswaan', [
                'judul'        => 'Ruang dari Guru ' . uniqid(),
                'deskripsi'    => 'Deskripsi buatan guru.',
                'isi'          => "Paragraf pertama.\n\nParagraf kedua.",
                'ikon'         => 'kegiatan',
                'tautan'       => 'pengumuman',
                'tautan_label' => 'Lihat Pengumuman',
                'urutan'       => 91,
            ])
            ->assertCreated();

        $id = $response->json('id');

        $this->actingAs($guru)
            ->putJson('/api/kesiswaan/' . $id, [
                'judul'     => 'Ruang dari Guru (revisi)',
                'deskripsi' => 'Deskripsi revisi.',
                'isi'       => 'Isi revisi.',
                'ikon'      => 'kegiatan',
            ])
            ->assertOk();

        $this->assertDatabaseHas('kesiswaan_items', [
            'id'    => $id,
            'judul' => 'Ruang dari Guru (revisi)',
        ]);
    }

    public function test_guru_tidak_bisa_menghapus_ruang(): void
    {
        $guru = $this->makeUser('guru');
        $room = $this->makeRoom();

        $this->actingAs($guru)
            ->deleteJson('/api/kesiswaan/' . $room->id)
            ->assertForbidden();

        $this->assertDatabaseHas('kesiswaan_items', ['id' => $room->id]);
    }

    public function test_guru_bisa_mengelola_pengumuman(): void
    {
        $guru = $this->makeUser('guru');

        $id = $this->actingAs($guru)
            ->postJson('/api/pengumuman', $this->announcementPayload())
            ->assertSuccessful()
            ->json('id');

        $this->actingAs($guru)
            ->putJson('/api/pengumuman/' . $id, $this->announcementPayload())
            ->assertOk();

        $this->actingAs($guru)
            ->deleteJson('/api/pengumuman/' . $id)
            ->assertNoContent();
    }

    public function test_guru_tidak_bisa_mengelola_konten_lain(): void
    {
        $guru = $this->makeUser('guru');

        $this->actingAs($guru)->postJson('/api/jadwal', [])->assertForbidden();
        $this->actingAs($guru)->postJson('/api/ekstrakurikuler', [])->assertForbidden();
        $this->actingAs($guru)->postJson('/api/ujian', [])->assertForbidden();
        $this->actingAs($guru)->putJson('/api/profil-sekolah', [])->assertForbidden();
        $this->actingAs($guru)->getJson('/api/siswa')->assertForbidden();
        $this->actingAs($guru)->getJson('/api/akun')->assertForbidden();
    }

    public function test_guru_bk_dan_siswa_tidak_bisa_mengelola_ruang_dan_pengumuman(): void
    {
        foreach (['guru_bk', 'siswa'] as $role) {
            $user = $this->makeUser($role);

            $this->actingAs($user)
                ->postJson('/api/kesiswaan', [
                    'judul'     => 'Ruang Terlarang',
                    'deskripsi' => 'Deskripsi.',
                ])
                ->assertForbidden();

            $this->actingAs($user)
                ->postJson('/api/pengumuman', $this->announcementPayload())
                ->assertForbidden();

            $this->flushSession();
        }
    }

    public function test_tamu_tidak_bisa_membuat_ruang_atau_pengumuman(): void
    {
        $this->postJson('/api/kesiswaan', [
            'judul'     => 'Ruang Tamu',
            'deskripsi' => 'Deskripsi.',
        ])->assertUnauthorized();

        $this->postJson('/api/pengumuman', $this->announcementPayload())
            ->assertUnauthorized();
    }

    public function test_dashboard_guru_hanya_menampilkan_tab_pengumuman_dan_kesiswaan(): void
    {
        $guru = $this->makeUser('guru');

        $response = $this->actingAs($guru)->get('/');

        $response->assertOk();
        $response->assertSee('data-admin-tab="pengumuman"', false);
        $response->assertSee('data-admin-tab="kesiswaan"', false);

        $response->assertDontSee('data-admin-tab="jadwal"', false);
        $response->assertDontSee('data-admin-tab="ekstra"', false);
        $response->assertDontSee('data-admin-tab="ujian"', false);
        $response->assertDontSee('data-admin-tab="siswa"', false);
        $response->assertDontSee('data-admin-tab="profil-sekolah"', false);
        $response->assertDontSee('data-admin-tab="buat-akun"', false);
    }

    public function test_menu_kesiswaan_tampil_untuk_guru(): void
    {
        $guru = $this->makeUser('guru');

        $this->actingAs($guru)
            ->get('/')
            ->assertOk()
            ->assertSee('data-target="kesiswaan"', false);
    }

    public function test_admin_tetap_melihat_semua_tab(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get('/')->assertOk();

        foreach (['pengumuman', 'jadwal', 'ekstra', 'kesiswaan', 'ujian', 'siswa', 'profil-sekolah', 'buat-akun'] as $tab) {
            $response->assertSee('data-admin-tab="' . $tab . '"', false);
        }
    }

    public function test_guru_bk_hanya_melihat_tab_konseling(): void
    {
        $guruBk = $this->makeUser('guru_bk');

        $response = $this->actingAs($guruBk)->get('/')->assertOk();

        $response->assertSee('data-admin-tab="konseling"', false);
        $response->assertDontSee('data-admin-tab="pengumuman"', false);
        $response->assertDontSee('data-admin-tab="kesiswaan"', false);
        $response->assertDontSee('data-admin-tab="jadwal"', false);
    }

    public function test_siswa_dan_tamu_tidak_melihat_tab_dashboard(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('data-admin-tab="pengumuman"', false)
            ->assertDontSee('data-admin-tab="kesiswaan"', false);

        $siswa = $this->makeUser('siswa');

        $this->actingAs($siswa)
            ->get('/')
            ->assertOk()
            ->assertDontSee('data-admin-tab="pengumuman"', false)
            ->assertDontSee('data-admin-tab="kesiswaan"', false);
    }
}
