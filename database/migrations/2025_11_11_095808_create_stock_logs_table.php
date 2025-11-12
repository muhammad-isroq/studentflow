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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();

            // Perubahan: +10 (masuk), -5 (keluar)
            $table->integer('change_amount'); 

            // Stok setelah perubahan terjadi
            $table->integer('stock_after_change'); 

            // Alasan: "Pembelian baru", "Dipinjam oleh: Roche", "Rusak"
            $table->string('reason');

            // Siapa yang mencatat
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
