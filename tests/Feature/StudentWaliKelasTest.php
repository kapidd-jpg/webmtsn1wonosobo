<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentWaliKelasTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role, array $attributes = []): User
    {
        return User::create(array_merge([
            'name'     => 'Uji ' . $role . ' ' . uniqid(),
            'nis_nip'  => 'uji-' . uniqid(),
            'role'     => $role,
            'password' => 'rahasia123',
        ], $attributes));
    }

    public function test_guru_melihat_wali_kelas_terisi_otomatis_dari_kelas_yang_sama(): void
    {
        $guru = $this->makeUser('guru');
        $kelas = 'UJI IX A ' . uniqid();

        $sumber = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Ibu Sumber, S.Pd.',
        ]);

        $kosong = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => null,
        ]);

        $rows = collect(
            $this->actingAs($guru)
                ->getJson('/api/siswa-kesiswaan')
                ->assertOk()
                ->json()
        )->keyBy('id');

        $this->assertSame('Ibu Sumber, S.Pd.', $rows[$sumber->id]['wali_kelas']);
        $this->assertSame('Ibu Sumber, S.Pd.', $rows[$kosong->id]['wali_kelas']);
    }

    public function test_wali_kelas_sendiri_tidak_tertimpa(): void
    {
        $guru = $this->makeUser('guru');
        $kelas = 'UJI IX B ' . uniqid();

        $satut = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Bu Satu, S.Pd.',
        ]);

        $dua = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Bu Dua, S.Pd.',
        ]);

        $rows = collect(
            $this->actingAs($guru)
                ->getJson('/api/siswa-kesiswaan')
                ->assertOk()
                ->json()
        )->keyBy('id');

        $this->assertSame('Bu Satu, S.Pd.', $rows[$satut->id]['wali_kelas']);
        $this->assertSame('Bu Dua, S.Pd.', $rows[$dua->id]['wali_kelas']);
    }

    public function test_kelas_tanpa_data_wali_kelas_tetap_kosong(): void
    {
        $guru = $this->makeUser('guru');
        $kelas = 'UJI IX C ' . uniqid();

        $siswa = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => null,
        ]);

        $rows = collect(
            $this->actingAs($guru)
                ->getJson('/api/siswa-kesiswaan')
                ->assertOk()
                ->json()
        )->keyBy('id');

        $this->assertNull($rows[$siswa->id]['wali_kelas']);
    }

    public function test_siswa_baru_otomatis_mendapat_wali_kelas_dari_kelasnya(): void
    {
        $admin = $this->makeUser('admin');
        $kelas = 'UJI IX D ' . uniqid();

        $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Pak Sumber, S.Pd.',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/api/siswa', [
                'name'     => 'Siswa Baru',
                'nis_nip'  => 'uji-baru-' . uniqid(),
                'role'     => 'siswa',
                'kelas'    => $kelas,
                'password' => 'rahasia123',
            ])
            ->assertCreated();

        $this->assertSame('Pak Sumber, S.Pd.', $response->json('wali_kelas'));
    }

    public function test_edit_siswa_dengan_wali_kelas_kosong_terisi_otomatis(): void
    {
        $admin = $this->makeUser('admin');
        $kelas = 'UJI IX F ' . uniqid();

        $sumber = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Bu Edit, S.Pd.',
        ]);

        $target = $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => null,
        ]);

        $response = $this->actingAs($admin)
            ->putJson('/api/siswa/' . $target->id, [
                'kelas'               => $kelas,
                'wali_kelas'          => '',
                'extracurricular_ids' => [],
            ])
            ->assertOk();

        $this->assertSame('Bu Edit, S.Pd.', $response->json('wali_kelas'));
        $this->assertSame('Bu Edit, S.Pd.', $target->fresh()->wali_kelas);
        $this->assertSame('Bu Edit, S.Pd.', $sumber->fresh()->wali_kelas);
    }

    public function test_buat_akun_guru_tidak_mendapat_wali_kelas(): void
    {
        $admin = $this->makeUser('admin');
        $kelas = 'UJI IX E ' . uniqid();

        $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => 'Bu Sumber, S.Pd.',
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/api/siswa', [
                'name'     => 'Guru Baru',
                'nis_nip'  => 'uji-guru-' . uniqid(),
                'role'     => 'guru',
                'kelas'    => $kelas,
                'password' => 'rahasia123',
            ])
            ->assertCreated();

        $this->assertNull($response->json('wali_kelas'));
        $this->assertNull($response->json('kelas'));
    }
}
