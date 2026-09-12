<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    public function index()
    {
        return User::where('role', 'siswa')
            ->with('extracurriculars:id,judul')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'nis_nip'  => 'required|string|max:30|unique:users,nis_nip',
            'role'     => 'required|in:siswa,guru',
            'kelas'    => 'nullable|string|max:50',
            'password' => ['required', 'string', Password::min(6)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'nis_nip'  => $data['nis_nip'],
            'role'     => $data['role'],
            'kelas'    => $data['role'] === 'siswa' ? $data['kelas'] : null,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'siswa') {
            abort(422, 'User yang dipilih bukan siswa.');
        }

        $data = $request->validate([
            'kelas'              => 'nullable|string|max:50',
            'wali_kelas'         => 'nullable|string|max:100',
            'kehadiran_persen'   => 'nullable|integer|min:0|max:100',
            'rata_rata_nilai'    => 'nullable|integer|min:0|max:100',
            'poin_prestasi'      => 'nullable|integer|min:0',
            'extracurricular_ids'   => 'nullable|array',
            'extracurricular_ids.*' => 'integer|exists:extracurriculars,id',
        ]);

        $user->update([
            'kelas'            => $data['kelas'] ?? $user->kelas,
            'wali_kelas'       => $data['wali_kelas'] ?? $user->wali_kelas,
            'kehadiran_persen' => $data['kehadiran_persen'] ?? null,
            'rata_rata_nilai'  => $data['rata_rata_nilai'] ?? null,
            'poin_prestasi'    => $data['poin_prestasi'] ?? 0,
        ]);

        $user->extracurriculars()->sync($data['extracurricular_ids'] ?? []);

        return $user->load('extracurriculars:id,judul');
    }

    public function me(Request $request)
    {
        return $request->user()->load('extracurriculars:id,judul');
    }
}