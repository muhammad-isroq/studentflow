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
        Schema::table('attendances', function (Blueprint $table) {
        $table->index('class_session_id'); // Mempercepat pengecekan absensi per sesi
        $table->index('siswa_id');
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->index('program_id'); // Mempercepat loading tab Class Session
            $table->index('guru_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_flow', function (Blueprint $table) {
            //
        });
    }
};
