<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('tanggal');
        $table->string('jam');
        $table->string('mapel');
        $table->string('kelas');
        $table->enum('jenis', ['Ulangan Harian', 'UTS', 'UAS']);
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('exams');
}
};
