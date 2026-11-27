<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_genre', function (Blueprint $table) {
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['game_id', 'genre_id']);
        });

        // Copy the current single genre of every game into the new table
        DB::statement('INSERT INTO game_genre (game_id, genre_id) SELECT id, genre_id FROM games WHERE genre_id IS NOT NULL');

        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('genre_id');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('genre_id')->nullable()->constrained()->restrictOnDelete();
        });

        // Keep the first genre of each game
        DB::statement('UPDATE games SET genre_id = (SELECT MIN(genre_id) FROM game_genre WHERE game_genre.game_id = games.id)');

        Schema::dropIfExists('game_genre');
    }
};
