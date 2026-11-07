<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPasswordTest extends TestCase
{
    use DatabaseTransactions;

    private function makeUser(string $role, array $attributes = []): User
    {
        return User::create(array_merge([
            'name'     => 'Uji ' . $role . ' ' . uniqid(),
            'nis_nip'  => 'akun-' . uniqid(),
            'role'     => $role,
            'password' => 'rahasia123',
        ], $attributes));
    }

    public function test_pengguna_bisa_mengganti_password_sendiri(): void
    {
        $user = $this->makeUser('siswa');

        $this->actingAs($user)
            ->putJson('/api/akun/password', [
                'current_password'      => 'rahasia123',
                'password'              => 'passwordBaru123',
                'password_confirmation' => 'passwordBaru123',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password berhasil diperbarui.');

        $this->assertTrue(Hash::check('passwordBaru123', $user->fresh()->password));
    }

    public function test_password_lama_salah_ditolak(): void
    {
        $user = $this->makeUser('guru');

        $this->actingAs($user)
            ->putJson('/api/akun/password', [
                'current_password'      => 'salah123',
                'password'              => 'passwordBaru123',
                'password_confirmation' => 'passwordBaru123',
            ])
            ->assertStatus(422);

        $this->assertTrue(Hash::check('rahasia123', $user->fresh()->password));
    }

    public function test_password_baru_minimal_delapan_karakter(): void
    {
        $user = $this->makeUser('guru_bk');

        $this->actingAs($user)
            ->putJson('/api/akun/password', [
                'current_password'      => 'rahasia123',
                'password'              => 'pendek',
                'password_confirmation' => 'pendek',
            ])
            ->assertStatus(422);
    }

    public function test_admin_bisa_reset_password_pengguna_lain(): void
    {
        $admin = $this->makeUser('admin');
        $siswa = $this->makeUser('siswa');

        $this->actingAs($admin)
            ->putJson('/api/akun/' . $siswa->id . '/password', [
                'password'              => 'passwordReset123',
                'password_confirmation' => 'passwordReset123',
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('passwordReset123', $siswa->fresh()->password));
    }

    public function test_admin_bisa_reset_password_sendiri_tanpa_kehilangan_sesi(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->putJson('/api/akun/' . $admin->id . '/password', [
                'password'              => 'passwordReset123',
                'password_confirmation' => 'passwordReset123',
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('passwordReset123', $admin->fresh()->password));

        // Sesi admin sendiri tetap valid setelah reset.
        $this->getJson('/api/akun')->assertOk();
    }

    public function test_bukan_admin_tidak_bisa_reset_password_pengguna_lain(): void
    {
        $guru = $this->makeUser('guru');
        $siswa = $this->makeUser('siswa');

        $this->actingAs($guru)
            ->putJson('/api/akun/' . $siswa->id . '/password', [
                'password'              => 'passwordReset123',
                'password_confirmation' => 'passwordReset123',
            ])
            ->assertForbidden();

        $this->assertTrue(Hash::check('rahasia123', $siswa->fresh()->password));
    }

    public function test_daftar_akun_hanya_untuk_admin_dan_tanpa_password(): void
    {
        $admin = $this->makeUser('admin');
        $siswa = $this->makeUser('siswa');

        $response = $this->actingAs($admin)
            ->getJson('/api/akun')
            ->assertOk();

        $rows = collect($response->json())->keyBy('id');
        $this->assertArrayHasKey($siswa->id, $rows->all());
        $this->assertArrayNotHasKey('password', $rows[$siswa->id]);

        // Ganti pengguna dalam sesi yang sama (middleware auth.session
        // mengunci sesi ke pengguna pertama).
        $this->flushSession();

        $this->actingAs($siswa)
            ->getJson('/api/akun')
            ->assertForbidden();
    }
}
