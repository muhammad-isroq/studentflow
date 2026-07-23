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
        Schema::create('teacher_recap_archives', function (Blueprint $table) {
            $table->id();
            // Asumsi nama tabel guru Anda adalah 'gurus'
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            
            // Kolom untuk periode arsip per bulan
            $table->tinyInteger('month');
            $table->integer('year');
            
            // Snapshot total
            $table->integer('total_sessions')->default(0);
            $table->integer('total_teaching_minutes')->default(0);
            
            // Menyimpan detail program dalam format JSON
            $table->json('program_details')->nullable();
            
            $table->timestamps();

            // Mencegah ada 2 arsip untuk 1 guru di bulan dan tahun yang sama
            $table->unique(['guru_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_recap_archives');
    }
};
