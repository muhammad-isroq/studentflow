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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            // Kolom untuk menghubungkan ke barang
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            
            // Kolom untuk tahu siapa yang mengubah
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Kuantitas: +10 (masuk) atau -5 (keluar)
            $table->integer('quantity'); 
            
            // Keterangan (Opsional tapi sangat disarankan)
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
