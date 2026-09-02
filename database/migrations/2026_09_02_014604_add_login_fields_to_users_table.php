<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis_nip')->unique()->after('name');
            $table->enum('role', ['siswa', 'guru'])->default('siswa')->after('nis_nip');
            $table->string('kelas')->nullable()->after('role');
            $table->string('wali_kelas')->nullable()->after('kelas');
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis_nip', 'role', 'kelas', 'wali_kelas']);
        });
    }
};