<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GameDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = collect(['PC', 'PlayStation 5', 'Xbox Series X|S', 'Nintendo Switch', 'Android', 'iOS'])
            ->mapWithKeys(fn($name) => [$name => Platform::firstOrCreate(['name' => $name])]);

        $genres = collect(['RPG', 'Action', 'Adventure', 'Simulation', 'Puzzle', 'Racing', 'MOBA'])
            ->mapWithKeys(fn($name) => [$name => Genre::firstOrCreate(['name' => $name])]);

        $console = ['PlayStation 5', 'Xbox Series X|S'];

        $games = [
            ['The Witcher 3: Wild Hunt', 'CD Projekt Red', 2015, 'RPG', ['PC', 'Nintendo Switch', ...$console]],
            ['Elden Ring', 'FromSoftware', 2022, 'RPG', ['PC', ...$console]],
            ['Genshin Impact', 'HoYoverse', 2020, 'RPG', ['PC', 'Android', 'iOS', 'PlayStation 5']],
            ['Hades', 'Supergiant Games', 2020, 'Action', ['PC', 'Nintendo Switch', ...$console]],
            ['Celeste', 'Maddy Makes Games', 2018, 'Adventure', ['PC', 'Nintendo Switch', ...$console]],
            ['Hollow Knight', 'Team Cherry', 2017, 'Adventure', ['PC', 'Nintendo Switch', ...$console]],
            ['Minecraft', 'Mojang Studios', 2011, 'Adventure', ['PC', 'Android', 'iOS', 'Nintendo Switch', ...$console]],
            ['Stardew Valley', 'ConcernedApe', 2016, 'Simulation', ['PC', 'Android', 'iOS', 'Nintendo Switch']],
            ['Portal 2', 'Valve', 2011, 'Puzzle', ['PC']],
            ['Forza Horizon 5', 'Playground Games', 2021, 'Racing', ['PC', 'Xbox Series X|S']],
            ['Mobile Legends: Bang Bang', 'Moonton', 2016, 'MOBA', ['Android', 'iOS']],
        ];

        foreach ($games as [$title, $developer, $year, $genre, $platformNames]) {
            $game = Game::firstOrCreate(
                ['title' => $title],
                [
                    'developer' => $developer,
                    'release_year' => $year,
                    'description' => "Contoh data untuk game {$title}.",
                ]
            );

            $game->genres()->syncWithoutDetaching([$genres[$genre]->id]);
            $game->platforms()->syncWithoutDetaching(
                collect($platformNames)->map(fn($name) => $platforms[$name]->id)->all()
            );
        }

        // Sample users and random reviews, so ratings and the slider have data
        $users = collect(range(1, 5))->map(function ($i) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@gamelibrary.test"],
                ['name' => "Pengguna {$i}", 'password' => Hash::make('password')]
            );
            $user->assignRole(Role::firstOrCreate(['name' => 'user']));

            return $user;
        });

        Game::all()->each(function ($game) use ($users) {
            $users->random(rand(2, 4))->each(function ($user) use ($game) {
                Review::firstOrCreate(
                    ['user_id' => $user->id, 'game_id' => $game->id],
                    ['rating' => rand(6, 10)]
                );
            });
        });
    }
}
