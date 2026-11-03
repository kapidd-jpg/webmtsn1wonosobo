<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis_nip')->unique();
            $table->string('role', 20)->default('siswa');
            $table->string('kelas')->nullable();
            $table->string('wali_kelas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis_nip', 'role', 'kelas', 'wali_kelas']);
        });
    }
};