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
        Schema::table('semester_reports', function (Blueprint $table) {
            if (Schema::hasColumn('semester_reports', 'nilai_rapor')) {
                $table->dropColumn('nilai_rapor');
            }

            // Tambahkan 5 kolom rapor per skill
            $table->decimal('rapor_listening', 5, 2)->nullable()->after('final_score');
            $table->decimal('rapor_reading', 5, 2)->nullable()->after('rapor_listening');
            $table->decimal('rapor_writing', 5, 2)->nullable()->after('rapor_reading');
            $table->decimal('rapor_speaking', 5, 2)->nullable()->after('rapor_writing');
            $table->decimal('rapor_grammar', 5, 2)->nullable()->after('rapor_speaking');
        });
    }

    public function down(): void
    {
        Schema::table('semester_reports', function (Blueprint $table) {
            $table->dropColumn(['rapor_listening', 'rapor_reading', 'rapor_writing', 'rapor_speaking', 'rapor_grammar']);
            $table->decimal('nilai_rapor', 5, 2)->nullable();
        });
    }
};
