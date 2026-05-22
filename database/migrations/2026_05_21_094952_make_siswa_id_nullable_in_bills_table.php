<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('bills', function (Blueprint $table) {
        // Mengubah kolom siswa_id agar boleh kosong (nullable)
        $table->unsignedBigInteger('siswa_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('bills', function (Blueprint $table) {
        // Mengembalikan ke wajib isi jika di-rollback
        $table->unsignedBigInteger('siswa_id')->nullable(false)->change();
    });
}
};
