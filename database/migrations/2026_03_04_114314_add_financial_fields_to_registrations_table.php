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
        Schema::table('registrations', function (Blueprint $table) {
            $table->decimal('spp_amount', 10, 2)->default(0)->after('program_id');
            $table->decimal('registration_fee', 10, 2)->default(0)->after('spp_amount');
            $table->date('tgl_masuk')->nullable()->after('registration_fee');
            $table->integer('billing_day')->nullable()->after('tgl_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            //
        });
    }
};
