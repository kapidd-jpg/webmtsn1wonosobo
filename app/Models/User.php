<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'nis_nip',
    'role',
    'kelas',
    'wali_kelas',
    'kehadiran_persen',
    'rata_rata_nilai',
    'poin_prestasi',
    'email',
    'password',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Nama lengkap untuk sapaan dashboard, tanpa gelar sapaan
     * ("Bpk.", "Ibu", "Pak", "Bu"), tetapi gelar akademik tetap ditampilkan.
     */
    public function greetingName(): string
    {
        $name = trim((string) $this->name);

        // Buang gelar sapaan di depan nama.
        $stripped = preg_replace(
            '/^(?:(?:bapak|bpk|pak|ibu|bu|saudari|saudara|sdri|sdr|prof|drs|dra|dr|hj|h)\.?\s+)+/i',
            '',
            $name
        );

        $stripped = trim((string) $stripped);

        // Rapikan tanda koma di nama, mis. "Dewi Kusuma, S.Pd." -> "Dewi Kusuma S.Pd.".
        $stripped = preg_replace('/\s*,\s*/', ' ', $stripped);
        $stripped = trim(preg_replace('/\s+/', ' ', (string) $stripped));

        if ($stripped === '') {
            $stripped = $name;
        }

        return $stripped;
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isGuruBk(): bool
    {
        return $this->role === 'guru_bk';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canManageContent(): bool
    {
        return $this->role === 'admin';
    }

    public function canManageKonseling(): bool
    {
        return in_array($this->role, ['guru_bk', 'admin'], true);
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function extracurriculars()
    {
        return $this->belongsToMany(Extracurricular::class);
    }

    public function counselingSubmissions()
    {
        return $this->hasMany(CounselingSubmission::class);
    }
}