<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;

class GameDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = collect(['PC', 'PlayStation 5', 'Xbox Series X|S', 'Nintendo Switch', 'Android', 'iOS'])
            ->mapWithKeys(fn($name) => [$name => Platform::firstOrCreate(['name' => $name])]);

        $rpg = Genre::firstOrCreate(['name' => 'RPG'], ['description' => 'Role-playing games']);
        $action = Genre::firstOrCreate(['name' => 'Action'], ['description' => 'Fast-paced action games']);

        $game = Game::firstOrCreate(
            ['title' => 'Sample RPG'],
            [
                'genre_id' => $rpg->id,
                'developer' => 'Dev Studio',
                'release_year' => 2022,
                'description' => 'Contoh game RPG.'
            ]
        );
        $game->platforms()->syncWithoutDetaching([$platforms['PC']->id, $platforms['PlayStation 5']->id]);

        $game2 = Game::firstOrCreate(
            ['title' => 'Sample Action'],
            [
                'genre_id' => $action->id,
                'developer' => 'Indie Team',
                'release_year' => 2023,
                'description' => 'Contoh game aksi.'
            ]
        );
        $game2->platforms()->syncWithoutDetaching([$platforms['PC']->id]);
    }
}
