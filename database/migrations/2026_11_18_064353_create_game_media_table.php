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
        Schema::create('game_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);            // 'image' or 'video'
            $table->string('path');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['game_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_media');
    }
};
