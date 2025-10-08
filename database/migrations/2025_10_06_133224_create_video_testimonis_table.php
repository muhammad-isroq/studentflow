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
        Schema::create('video_testimonis', function (Blueprint $table) {
            $table->id();
            $table->string('link_video'); 
            $table->string('notes1');     // Untuk judul testimoni, cth: "Anak Saya Jadi Lebih Percaya Diri!"
            $table->text('notes2');       // Untuk isi testimoni yang lebih panjang
            $table->string('nama_ortu');  // Nama orang tua
            $table->string('nama_anak');  // Nama siswa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_testimonis');
    }
};
