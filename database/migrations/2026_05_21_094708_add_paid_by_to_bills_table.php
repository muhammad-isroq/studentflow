<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('bills', function (Blueprint $table) {
        // Menambahkan kolom paid_by setelah kolom siswa_id
        $table->string('paid_by')->nullable()->after('siswa_id');
    });
}

public function down(): void
{
    Schema::table('bills', function (Blueprint $table) {
        $table->dropColumn('paid_by');
    });
}
};
