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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->string('kelas_disekolah');
            $table->string('no_wali');
            $table->string('foto_formulir');
            $table->text('alamat');
            $table->date('tgl_lahir');
            $table->date('tgl_masuk');
            $table->date('tgl_registrasi');
            $table->foreignId('program_id')->constrained('programs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
