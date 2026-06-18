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
            $table->decimal('nilai_rapor', 5, 2)->default(0)->after('final_score');
            $table->integer('rank')->default(0)->after('nilai_rapor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semester_reports', function (Blueprint $table) {
            //
        });
    }
};
