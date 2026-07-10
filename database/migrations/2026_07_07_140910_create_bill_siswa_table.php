<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::create('bill_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->timestamps();
        });

        
        $bills = DB::table('bills')->whereNotNull('siswa_id')->get();
        
        foreach ($bills as $bill) {
            DB::table('bill_siswa')->insert([
                'bill_id' => $bill->id,
                'siswa_id' => $bill->siswa_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn('siswa_id');
        });
    }

    public function down(): void
    {
        
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('siswa_id')->nullable()->constrained('siswas');
        });

        
        Schema::dropIfExists('bill_siswa');
    }
};
