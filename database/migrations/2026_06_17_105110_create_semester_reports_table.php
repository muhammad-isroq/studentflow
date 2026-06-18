<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            
            // Nama/Periode Semester (misal: "Ganjil 2026/2027" atau "Juli - Desember 2026")
            $table->string('semester_name'); 
            
            // Menyimpan angka mati hasil kalkulasi
            $table->decimal('avg_listening', 5, 2)->default(0);
            $table->decimal('avg_reading', 5, 2)->default(0);
            $table->decimal('avg_writing', 5, 2)->default(0);
            $table->decimal('avg_speaking', 5, 2)->default(0);
            $table->decimal('avg_grammar', 5, 2)->default(0);
            $table->decimal('total_score', 6, 2)->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_reports');
    }
};
