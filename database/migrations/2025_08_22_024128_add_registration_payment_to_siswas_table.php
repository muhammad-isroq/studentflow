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
            $table->decimal('registration_fee', 10, 2)->nullable()->after('program_id');
            $table->date('registration_payment_date')->nullable()->after('registration_fee');
            $table->string('registration_proof')->nullable()->after('registration_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
             $table->dropColumn(['registration_fee', 'registration_payment_date', 'registration_proof']);
        });
    }
};
