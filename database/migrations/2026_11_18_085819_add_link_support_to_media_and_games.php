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
        Schema::table('game_media', function (Blueprint $table) {
            $table->string('path')->nullable()->change();
            $table->string('source', 10)->default('upload')->after('type'); // upload | link
            $table->string('link_url', 2048)->nullable()->after('path');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->string('cover_link', 2048)->nullable()->after('cover');
        });
    }

    public function down(): void
    {
        Schema::table('game_media', function (Blueprint $table) {
            $table->dropColumn(['source', 'link_url']);
            $table->string('path')->nullable(false)->change();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('cover_link');
        });
    }
};
