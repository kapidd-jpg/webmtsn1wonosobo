<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('kehadiran_persen')->nullable()->after('wali_kelas');
            $table->unsignedTinyInteger('rata_rata_nilai')->nullable()->after('kehadiran_persen');
            $table->unsignedInteger('poin_prestasi')->default(0)->after('rata_rata_nilai');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kehadiran_persen', 'rata_rata_nilai', 'poin_prestasi']);
        });
    }
};