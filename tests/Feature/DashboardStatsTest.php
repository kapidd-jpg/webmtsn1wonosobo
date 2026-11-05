<?php

namespace Tests\Feature;

use App\Models\CounselingSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use DatabaseTransactions;

    private function guru(string $role): User
    {
        return User::create([
            'name'     => 'Guru Uji ' . $role,
            'nis_nip'  => 'uji-' . $role . '-' . uniqid(),
            'role'     => $role,
            'password' => 'rahasia123',
        ]);
    }

    public function test_guru_bk_mendapat_ringkasan_pengajuan(): void
    {
        $bk = $this->guru('guru_bk');

        $totalAwal = CounselingSubmission::count();
        $selesaiAwal = CounselingSubmission::where('sudah_ditangani', true)
            ->orWhere('status', 'selesai')
            ->count();

        CounselingSubmission::create([
            'nama' => 'Siswa A', 'kelas' => 'IX A', 'masalah' => 'Uji masuk',
        ]);

        CounselingSubmission::create([
            'nama' => 'Siswa B', 'kelas' => 'IX B', 'masalah' => 'Uji selesai',
            'sudah_ditangani' => true,
        ]);

        $this->actingAs($bk)
            ->getJson('/api/dashboard-guru')
            ->assertOk()
            ->assertJsonStructure([
                'pengajuan_masuk',
                'belum_ditangani',
                'kasus_selesai',
                'siswa_aktif_ekskul',
                'total_siswa',
            ])
            ->assertJson([
                'pengajuan_masuk' => $totalAwal + 2,
                'kasus_selesai'   => $selesaiAwal + 1,
            ]);
    }

    public function test_siswa_tidak_boleh_mengakses_ringkasan_guru(): void
    {
        $siswa = $this->guru('siswa');

        $this->actingAs($siswa)
            ->getJson('/api/dashboard-guru')
            ->assertForbidden();
    }
}
