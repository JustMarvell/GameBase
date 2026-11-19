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
        Schema::table('games', function (Blueprint $table) {
            $table->unsignedTinyInteger('cover_focus_x')->default(50)->after('cover_link');
            $table->unsignedTinyInteger('cover_focus_y')->default(50)->after('cover_focus_x');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['cover_focus_x', 'cover_focus_y']);
        });
    }
};
