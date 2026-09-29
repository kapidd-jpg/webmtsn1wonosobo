<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah', 'kepala_sekolah', 'alamat', 'telepon', 'email',
        'tahun_berdiri', 'akreditasi', 'visi', 'misi', 'sejarah_singkat',
    ];
}