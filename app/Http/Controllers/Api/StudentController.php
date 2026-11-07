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
        $students = User::where('role', 'siswa')
            ->with('extracurriculars:id,judul')
            ->orderBy('name')
            ->get();

        return $this->resolveWaliKelas($students);
    }

    /**
     * Daftar siswa untuk guru kesiswaan. Hanya memuat data yang
     * dibutuhkan untuk ditampilkan, tanpa email atau kredensial.
     */
    public function indexForGuru()
    {
        $students = User::where('role', 'siswa')
            ->with('extracurriculars:id,judul')
            ->orderBy('kelas')
            ->orderBy('name')
            ->get(['id', 'name', 'nis_nip', 'kelas', 'wali_kelas']);

        return $this->resolveWaliKelas($students);
    }

    /**
     * Daftar wali kelas per kelas, diambil dari data siswa.
     */
    public function waliKelasList()
    {
        $students = User::where('role', 'siswa')
            ->whereNotNull('kelas')
            ->get(['id', 'kelas', 'wali_kelas']);

        $students = $this->resolveWaliKelas($students);

        $classes = [];

        foreach ($students as $student) {
            $key = $this->normalizeKelas($student->kelas);

            if ($key === '') {
                continue;
            }

            if (! isset($classes[$key])) {
                $classes[$key] = [
                    'kelas'        => trim((string) $student->kelas),
                    'wali_kelas'   => $student->wali_kelas,
                    'jumlah_siswa' => 0,
                ];
            }

            $classes[$key]['jumlah_siswa']++;

            if (blank($classes[$key]['wali_kelas']) && filled($student->wali_kelas)) {
                $classes[$key]['wali_kelas'] = $student->wali_kelas;
            }
        }

        usort($classes, fn ($a, $b) => strnatcasecmp($a['kelas'], $b['kelas']));

        return response()->json(array_values($classes));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'nis_nip'    => 'required|string|max:30|unique:users,nis_nip',
            'role'       => 'required|in:siswa,guru,guru_bk,admin',
            'kelas'      => 'nullable|string|max:50',
            'wali_kelas' => 'nullable|string|max:100',
            'password'   => ['required', 'string', Password::min(8)],
        ]);

        $waliKelas = null;

        if ($data['role'] === 'siswa') {
            $waliKelas = $data['wali_kelas'] ?? null;

            if (blank($waliKelas)) {
                $waliKelas = $this->waliKelasForKelas($data['kelas'] ?? null);
            }
        }

        $user = User::create([
            'name'       => $data['name'],
            'nis_nip'    => $data['nis_nip'],
            'role'       => $data['role'],
            'kelas'      => $data['role'] === 'siswa' ? $data['kelas'] : null,
            'wali_kelas' => $waliKelas,
            'password'   => Hash::make($data['password']),
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

        $kelas = $data['kelas'] ?? $user->kelas;
        $waliKelas = $data['wali_kelas'] ?? $user->wali_kelas;

        if (blank($waliKelas)) {
            $waliKelas = $this->waliKelasForKelas($kelas, $user->id);
        }

        $user->update([
            'kelas'            => $kelas,
            'wali_kelas'       => $waliKelas,
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

    /**
     * Isi wali kelas yang kosong dengan wali kelas siswa lain di kelas
     * yang sama. Nilai ini hanya untuk ditampilkan, tidak disimpan.
     */
    private function resolveWaliKelas($students)
    {
        $votes = [];

        foreach ($students as $student) {
            $kelas = $this->normalizeKelas($student->kelas);
            $wali = trim((string) $student->wali_kelas);

            if ($kelas === '' || $wali === '') {
                continue;
            }

            $votes[$kelas][$wali] = ($votes[$kelas][$wali] ?? 0) + 1;
        }

        $resolved = [];

        foreach ($votes as $kelas => $candidates) {
            arsort($candidates);
            $resolved[$kelas] = array_key_first($candidates);
        }

        foreach ($students as $student) {
            if (trim((string) $student->wali_kelas) !== '') {
                continue;
            }

            $kelas = $this->normalizeKelas($student->kelas);
            $student->wali_kelas = $resolved[$kelas] ?? null;
            $student->syncOriginalAttribute('wali_kelas');
        }

        return $students;
    }

    /**
     * Cari wali kelas yang paling banyak dipakai pada satu kelas.
     */
    private function waliKelasForKelas(?string $kelas, ?int $exceptUserId = null): ?string
    {
        $key = $this->normalizeKelas($kelas);

        if ($key === '') {
            return null;
        }

        $rows = User::where('role', 'siswa')
            ->whereNotNull('kelas')
            ->whereNotNull('wali_kelas')
            ->where('wali_kelas', '!=', '')
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->get(['kelas', 'wali_kelas']);

        $votes = [];

        foreach ($rows as $row) {
            if ($this->normalizeKelas($row->kelas) !== $key) {
                continue;
            }

            $wali = trim((string) $row->wali_kelas);

            if ($wali === '') {
                continue;
            }

            $votes[$wali] = ($votes[$wali] ?? 0) + 1;
        }

        if ($votes === []) {
            return null;
        }

        arsort($votes);

        return array_key_first($votes);
    }

    private function normalizeKelas(?string $kelas): string
    {
        return mb_strtolower(trim((string) $kelas));
    }
}
