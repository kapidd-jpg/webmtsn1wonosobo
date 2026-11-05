<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnnouncementDateTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::create([
            'name'     => 'Admin Uji Tanggal',
            'nis_nip'  => 'uji-admin-' . uniqid(),
            'role'     => 'admin',
            'password' => 'rahasia123',
        ]);
    }

    public function test_pengumuman_menyimpan_tanggal_lengkap(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/pengumuman', [
                'tanggal'   => '2026-11-05',
                'bulan'     => 'NOV',
                'kategori'  => 'Akademik',
                'judul'     => 'Uji tanggal lengkap',
                'deskripsi' => 'Memastikan tahun tidak hilang.',
            ])
            ->assertCreated();

        $this->assertSame('2026-11-05', $response->json('tanggal'));

        $this->assertDatabaseHas('announcements', [
            'id'      => $response->json('id'),
            'tanggal' => '2026-11-05',
            'bulan'   => 'NOV',
        ]);
    }

    public function test_format_tanggal_lama_tetap_diterima(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/pengumuman', [
                'tanggal'   => '05',
                'bulan'     => 'Nov',
                'kategori'  => 'Kegiatan',
                'judul'     => 'Uji format lama',
                'deskripsi' => 'Masih mendukung tanggal dan bulan terpisah.',
            ])
            ->assertCreated();

        $this->assertSame('05', $response->json('tanggal'));
        $this->assertSame('Nov', $response->json('bulan'));
    }

    public function test_edit_pengumuman_tetap_menyimpan_tanggal_lengkap(): void
    {
        $announcement = Announcement::create([
            'tanggal'   => '02',
            'bulan'     => 'Sep',
            'kategori'  => 'Akademik',
            'judul'     => 'Data lama',
            'deskripsi' => 'Akan diperbarui dengan tanggal lengkap.',
        ]);

        $this->actingAs($this->admin())
            ->putJson('/api/pengumuman/' . $announcement->id, [
                'tanggal'   => '2025-09-02',
                'bulan'     => 'SEP',
                'kategori'  => 'Akademik',
                'judul'     => 'Data baru',
                'deskripsi' => 'Tahun 2025 tersimpan.',
            ])
            ->assertOk()
            ->assertJson([
                'tanggal' => '2025-09-02',
                'bulan'   => 'SEP',
            ]);

        $this->assertDatabaseHas('announcements', [
            'id'        => $announcement->id,
            'tanggal'   => '2025-09-02',
            'judul'     => 'Data baru',
        ]);
    }
}
