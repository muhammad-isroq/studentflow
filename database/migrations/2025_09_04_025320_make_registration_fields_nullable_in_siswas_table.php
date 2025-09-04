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
            $table->decimal('registration_fee', 10, 2)->nullable()->change();
            $table->string('registration_proof')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->decimal('registration_fee', 10, 2)->nullable(false)->change();
            $table->string('registration_proof')->nullable(false)->change();
        });
    }
};
