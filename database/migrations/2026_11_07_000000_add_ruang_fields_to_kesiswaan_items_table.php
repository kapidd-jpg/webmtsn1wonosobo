<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kesiswaan_items', function (Blueprint $table) {
            $table->string('slug', 160)->nullable()->unique();
            $table->string('ikon', 50)->nullable();
            $table->longText('isi')->nullable();
            $table->string('tautan')->nullable();
            $table->string('tautan_label', 60)->nullable();
        });

        // Isi slug untuk konten lama supaya tiap ruang punya identitas tetap.
        $items = DB::table('kesiswaan_items')
            ->whereNull('slug')
            ->orderBy('id')
            ->get(['id', 'judul']);

        foreach ($items as $item) {
            $base = Str::slug((string) $item->judul) ?: 'ruang-' . $item->id;
            $slug = $base;
            $counter = 2;

            while (DB::table('kesiswaan_items')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $counter;
                $counter++;
            }

            DB::table('kesiswaan_items')
                ->where('id', $item->id)
                ->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('kesiswaan_items', function (Blueprint $table) {
            $table->dropUnique('kesiswaan_items_slug_unique');
            $table->dropColumn(['slug', 'ikon', 'isi', 'tautan', 'tautan_label']);
        });
    }
};
