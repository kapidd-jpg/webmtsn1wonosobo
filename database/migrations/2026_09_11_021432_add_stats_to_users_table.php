<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('kehadiran_persen')->nullable();
            $table->unsignedTinyInteger('rata_rata_nilai')->nullable();
            $table->unsignedInteger('poin_prestasi')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kehadiran_persen', 'rata_rata_nilai', 'poin_prestasi']);
        });
    }
};