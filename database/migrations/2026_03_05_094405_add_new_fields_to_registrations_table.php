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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('nama');
            $table->string('agama')->nullable()->after('jenis_kelamin');
            $table->string('asal_sekolah')->nullable()->after('grade');
            $table->string('nama_orang_tua')->nullable()->after('no_wa_wali');
            $table->string('pekerjaan_orang_tua')->nullable()->after('nama_orang_tua');
            $table->string('sumber_info')->nullable()->after('bukti_pembayaran');
            $table->text('alasan_kursus')->nullable()->after('sumber_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            //
        });
    }
};
