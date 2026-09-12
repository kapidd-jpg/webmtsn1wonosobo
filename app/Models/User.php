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

    public function isGuru(): bool
    {
        return $this->role === 'guru';
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