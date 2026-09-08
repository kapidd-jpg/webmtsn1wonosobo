<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KesiswaanItem extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'deskripsi', 'urutan'];
}