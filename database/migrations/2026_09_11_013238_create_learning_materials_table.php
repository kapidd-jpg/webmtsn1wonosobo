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
    Schema::create('learning_materials', function (Blueprint $table) {
        $table->id();
        $table->string('mapel');
        $table->string('judul');
        $table->string('kelas');
        $table->text('deskripsi');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('learning_materials');
}
};
