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
        Schema::table('siswas', function (Blueprint $table) {
            
            if (Schema::hasColumn('siswas', 'nilai_rapor')) {
                $table->dropColumn('nilai_rapor');
            }
            
            
            $table->decimal('rapor_listening', 5, 2)->nullable()->after('program_id');
            $table->decimal('rapor_reading', 5, 2)->nullable()->after('rapor_listening');
            $table->decimal('rapor_writing', 5, 2)->nullable()->after('rapor_reading');
            $table->decimal('rapor_grammar', 5, 2)->nullable()->after('rapor_writing');
            $table->decimal('rapor_speaking', 5, 2)->nullable()->after('rapor_grammar');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'rapor_listening',
                'rapor_reading',
                'rapor_writing',
                'rapor_grammar',
                'rapor_speaking'
            ]);
            
            // Kembalikan kolom lama jika di-rollback
            $table->decimal('nilai_rapor', 5, 2)->nullable(); 
        });
    }
};
