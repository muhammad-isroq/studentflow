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
        Schema::create('attendance_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
        $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();

        // Kolom untuk semester/periode
        $table->string('semester_name'); // Misal: "Semester Ganjil 2024/2025"

        // Kolom "snapshot" (hasil perhitungan)
        $table->integer('total_hadir');
        $table->integer('total_sesi');
        $table->float('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_recaps');
    }
};
