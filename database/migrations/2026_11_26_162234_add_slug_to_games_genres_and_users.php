<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = ['games' => 'title', 'genres' => 'name', 'users' => 'name'];

    public function up(): void
    {
        foreach ($this->tables as $table => $source) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('slug')->nullable()->after('id');
            });

            $this->backfill($table, $source);

            Schema::table($table, function (Blueprint $t) {
                $t->unique('slug');
            });
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropUnique(['slug']);
                $t->dropColumn('slug');
            });
        }
    }

    /** Gives every existing row a unique slug. */
    private function backfill(string $table, string $source): void
    {
        $used = [];

        DB::table($table)->orderBy('id')->get(['id', $source])->each(function ($row) use ($table, $source, &$used) {
            $base = Str::slug((string) $row->{$source}) ?: Str::singular($table);

            if (ctype_digit($base)) {
                $base = Str::singular($table).'-'.$base; // avoid slugs that look like IDs
            }

            $slug = $base;
            $i = 2;
            while (isset($used[$slug])) {
                $slug = $base.'-'.$i++;
            }

            $used[$slug] = true;
            DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
        });
    }
};
