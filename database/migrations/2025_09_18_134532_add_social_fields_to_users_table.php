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
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('remember_token');
            $table->string('position')->nullable()->after('photo');
            $table->string('instagram_url')->nullable()->after('position');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'position', 'instagram_url', 'linkedin_url']);
        });
    }
};
