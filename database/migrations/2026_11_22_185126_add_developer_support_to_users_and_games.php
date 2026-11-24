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
            $table->string('website')->nullable()->after('bio');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('developer_id')->nullable()->after('genre_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropConstrainedForeignId('developer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('website');
        });
    }
};
