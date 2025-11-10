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
        Schema::table('attendance_recaps', function (Blueprint $table) {
            $table->dropForeign('attendance_recaps_guru_id_foreign');
            $table->foreign('guru_id')
                  ->references('id')
                  ->on('gurus') // <-- Ini yang benar
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_recaps', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);

            // 2. Kembalikan yang salah (jika di-rollback)
            $table->foreign('guru_id', 'attendance_recaps_guru_id_foreign')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
