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
    Schema::create('extracurriculars', function (Blueprint $table) {
        $table->id();
        $table->string('icon', 10);
        $table->enum('kategori', ['olahraga', 'seni', 'organisasi']);
        $table->string('judul');
        $table->string('jadwal');
        $table->string('lokasi');
        $table->text('deskripsi');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracurriculars');
    }
};
