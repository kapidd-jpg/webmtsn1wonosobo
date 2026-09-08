<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselingSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nama', 'kelas', 'masalah', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}