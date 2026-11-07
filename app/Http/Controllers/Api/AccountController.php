<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Ubah password akun yang sedang login.
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password baru tidak sama.',
            'password.min'                      => 'Password baru minimal 8 karakter.',
        ]);

        $user = $request->user();
        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // Regenerasi sesi saat ini. Middleware auth.session akan menyimpan
        // hash password baru sehingga perangkat lain otomatis keluar.
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Password berhasil diperbarui.',
        ]);
    }

    /**
     * Daftar akun untuk panel admin. Password tidak pernah dikirim.
     */
    public function index()
    {
        return User::orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'guru_bk' THEN 2 WHEN 'guru' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get(['id', 'name', 'nis_nip', 'role', 'kelas']);
    }

    /**
     * Reset password pengguna oleh admin.
     */
    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password.min'       => 'Password baru minimal 8 karakter.',
        ]);

        $target = $user->is($request->user()) ? $request->user() : $user;

        $target->forceFill(['password' => Hash::make($data['password'])])->save();

        return response()->json([
            'message' => 'Password akun ' . $user->name . ' berhasil direset.',
        ]);
    }
}
