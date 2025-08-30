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
        Schema::table('siswas', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama
            $table->dropForeign(['program_id']);

            // 2. Ubah kolom program_id menjadi boleh kosong (nullable)
            $table->foreignId('program_id')->nullable()->change();

            // 3. Tambahkan kembali foreign key dengan aturan baru 'onDelete('set null')'
            $table->foreign('program_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->foreignId('program_id')->nullable(false)->change();
            $table->foreign('program_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('cascade'); // Kembalikan ke aturan awal atau cascade
        });
    }
};
