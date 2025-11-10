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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang'); // Misal: Proyektor, Laptop, Meja Kelas
            $table->string('kode_aset')->unique()->nullable(); // Misal: KLS-001, LPT-002
            $table->string('kategori'); // Misal: Elektronik, Furnitur, Bahan Ajar
            $table->integer('jumlah')->default(1);
            $table->string('lokasi')->nullable(); // Misal: Ruang Kelas 1, Kantor, Gudang
            $table->enum('status', ['Baik', 'Rusak', 'Dipinjam', 'Perbaikan'])->default('Baik');
            
            // Kolom ini menghubungkan ke tabel user (guru/staff)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
