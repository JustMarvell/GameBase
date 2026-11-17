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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->restrictOnDelete();
            $table->string('title', 255);
            $table->string('developer', 100);
            $table->integer('release_year');
            $table->string('platform', 100);
            $table->decimal('rating', 3, 1)->default(0); // 0.0 - 10.0
            $table->string('cover')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
