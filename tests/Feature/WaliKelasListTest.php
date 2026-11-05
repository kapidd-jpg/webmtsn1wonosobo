<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WaliKelasListTest extends TestCase
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

    public function test_daftar_wali_kelas_unik_terurut_dan_menghitung_siswa(): void
    {
        $guru = $this->makeUser('guru');
        $suffix = uniqid();

        $kelasDua = 'UJI 2 ' . $suffix;
        $kelasSepuluh = 'UJI 10 ' . $suffix;

        $this->makeUser('siswa', [
            'kelas'      => $kelasDua,
            'wali_kelas' => 'Bu Dua, S.Pd.',
        ]);

        $this->makeUser('siswa', [
            'kelas'      => $kelasDua,
            'wali_kelas' => null,
        ]);

        $this->makeUser('siswa', [
            'kelas'      => $kelasSepuluh,
            'wali_kelas' => 'Pak Sepuluh, S.Pd.',
        ]);

        $rows = collect(
            $this->actingAs($guru)
                ->getJson('/api/wali-kelas')
                ->assertOk()
                ->json()
        )->filter(fn ($row) => str_contains($row['kelas'], $suffix))->values();

        $this->assertCount(2, $rows);

        $this->assertSame($kelasDua, $rows[0]['kelas']);
        $this->assertSame('Bu Dua, S.Pd.', $rows[0]['wali_kelas']);
        $this->assertSame(2, $rows[0]['jumlah_siswa']);

        $this->assertSame($kelasSepuluh, $rows[1]['kelas']);
        $this->assertSame('Pak Sepuluh, S.Pd.', $rows[1]['wali_kelas']);
        $this->assertSame(1, $rows[1]['jumlah_siswa']);
    }

    public function test_kelas_tanpa_wali_kelas_tetap_muncul(): void
    {
        $guru = $this->makeUser('guru');
        $kelas = 'UJI TANPA WALI ' . uniqid();

        $this->makeUser('siswa', [
            'kelas'      => $kelas,
            'wali_kelas' => null,
        ]);

        $rows = collect(
            $this->actingAs($guru)
                ->getJson('/api/wali-kelas')
                ->assertOk()
                ->json()
        )->keyBy('kelas');

        $this->assertArrayHasKey($kelas, $rows->all());
        $this->assertNull($rows[$kelas]['wali_kelas']);
        $this->assertSame(1, $rows[$kelas]['jumlah_siswa']);
    }

    public function test_role_selain_guru_tidak_boleh_mengakses_daftar_wali_kelas(): void
    {
        $this->getJson('/api/wali-kelas')->assertUnauthorized();

        $this->actingAs($this->makeUser('guru_bk'))
            ->getJson('/api/wali-kelas')
            ->assertForbidden();

        $this->actingAs($this->makeUser('siswa'))
            ->getJson('/api/wali-kelas')
            ->assertForbidden();
    }
}
