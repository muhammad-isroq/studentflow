<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_grading_system_tables.php
public function up(): void
{

    Schema::create('assessments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('program_id')->constrained()->cascadeOnDelete(); 
        $table->string('name'); 
        $table->integer('order')->default(1); 
        $table->timestamps();
    });

    // 2. Tabel Nilai Siswa
    Schema::create('grades', function (Blueprint $table) {
        $table->id();
        $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
        $table->foreignId('student_id')->constrained('siswas')->cascadeOnDelete(); 
        
        
        $table->integer('listening')->nullable();
        $table->integer('reading')->nullable();
        $table->integer('writing')->nullable();
        $table->integer('grammar')->nullable();
        $table->integer('speaking')->nullable();
        
        $table->decimal('average', 5, 2)->nullable();
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_system_tables');
    }
};
