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
    Schema::create('registrations', function (Blueprint $table) { 
        $table->id();
        // Data Akun & Login
        $table->string('email')->unique();
        $table->string('password');
        
        // Data Diri Siswa
        $table->string('nama');
        $table->string('grade'); 
        $table->string('photo')->nullable();
        $table->string('no_wa_wali'); 
        $table->text('alamat');
        $table->date('tgl_lahir');
        $table->date('tgl_registrasi')->default(now());

        // Tahapan Pendaftaran
        $table->string('bukti_pembayaran')->nullable();
        $table->enum('status', ['pending', 'waiting_verification', 'paid', 'rejected'])
              ->default('pending');
        
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
