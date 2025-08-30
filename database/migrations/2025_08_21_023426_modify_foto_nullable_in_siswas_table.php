<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('siswas')->whereNull('foto_formulir')->update(['foto_formulir' => 'TIDAK_ADA.jpg']);
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('foto')->nullable()->change();
            $table->string('foto_formulir')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('foto')->nullable(false)->change();
            $table->string('foto_formulir')->nullable()->change();
        });
    }
};
