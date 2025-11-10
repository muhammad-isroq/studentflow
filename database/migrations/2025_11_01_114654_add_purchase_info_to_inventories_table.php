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
        Schema::table('inventories', function (Blueprint $table) {
            $table->date('tanggal_beli')->nullable()->after('user_id');
            $table->decimal('harga', 15, 2)->nullable()->after('tanggal_beli');
            $table->string('bukti_pembelian')->nullable()->after('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['tanggal_beli', 'harga', 'bukti_pembelian']);
        });
    }
};
