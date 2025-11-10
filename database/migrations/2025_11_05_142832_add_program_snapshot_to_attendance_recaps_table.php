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
            $table->string('nama_program')->after('siswa_id');
            $table->string('nama_ruangan')->nullable()->after('nama_program');
            $table->string('jadwal_program')->nullable()->after('nama_ruangan');

            // Asumsi saya 'guru_id' terhubung ke tabel 'users'
            $table->foreignId('guru_id')->nullable()->after('jadwal_program')->constrained('users')->nullOnDelete(); 

            $table->string('jam_pelajaran')->nullable()->after('guru_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_recaps', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['guru_id']);

            // Hapus 5 kolom
            $table->dropColumn([
                'nama_program',
                'nama_ruangan',
                'jadwal_program',
                'guru_id',
                'jam_pelajaran'
            ]);
        });
    }
};
