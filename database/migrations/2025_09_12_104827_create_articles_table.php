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
        Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique(); // Untuk URL yang rapi, cth: /artikel/judul-artikel-ini
        $table->text('excerpt'); // Ringkasan singkat artikel
        $table->longText('body'); // Isi lengkap artikel
        $table->string('image')->nullable(); // Path ke gambar artikel
        $table->timestamp('published_at')->nullable(); // Kapan artikel dipublikasikan
        $table->timestamps(); // Otomatis membuat created_at dan updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
