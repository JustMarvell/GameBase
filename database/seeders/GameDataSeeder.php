<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use App\Services\GameCsvImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GameDataSeeder extends Seeder
{
    public function run(): void
    {
        $report = app(GameCsvImporter::class)->run(database_path('seeders/data/seeder.csv'), 'pattern', true);

        if ($report['fatal']) {
            $this->command->error($report['fatal']);

            return;
        }

        $usernames = [
            1 => 'Gerald01Gaming',
            2 => 'HizzGanzz',
            3 => 'VelloSaurus',
            4 => 'Dokja',
            5 => 'WnYh',
        ];

        $userRole = Role::firstOrCreate(['name' => 'user']);

        $users = collect($usernames)->map(function ($name, $i) use ($userRole) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@gamelibrary.test"],
                ['name' => $name, 'password' => Hash::make('password')]
            );
            $user->assignRole($userRole);

            return $user;
        });

        $reviews = [
            [1, 'Apex', 8, 'Satu match lagi, katanya. Tahu-tahu sudah subuh. Gerakannya mulus banget, tapi teman satu squad sering kabur pas lawan datang.'],
            [1, 'Cyberpunk', 9, 'Awal rilis memang bikin emosi, tapi sekarang Night City cantik banget. Ngobrol sama Johnny Silverhand bikin betah.'],
            [1, 'Counter-Strike', 7, 'Suara langkah kaki lebih penting daripada aim. Kalau kalah, tinggal salahkan teman satu tim.'],
            [1, 'Marvel Rivals', 8, 'Main Doctor Strange tapi portalnya malah dipakai musuh. Tetap ketagihan, karakternya keren semua.'],
            [1, 'Red Dead', 9, 'Saking detailnya, kuda kena dingin pun kelihatan. Ceritanya bikin mata berkaca-kaca di akhir.'],
            [1, 'PUBG', 7, 'Zona biru selalu datang tepat saat lagi loot. Seru, tapi tensinya bikin tangan keringetan.'],
            [1, 'Dawn of War', 9, 'Dentuman senjatanya mantap banget. Perang besar, drama kecil, semuanya pas.'],

            [2, 'Red Dead', 10, 'Cuma naik kuda sambil mancing aja bisa berjam-jam. Arthur Morgan itu legenda, titik.'],
            [2, 'Marvel Rivals', 8, 'Pilih Spider-Man, tapi nyangkut di tembok terus. Tetap seru dan gratis, mantap.'],
            [2, 'Grand Theft', 9, 'Niatnya ngerjain misi, ujungnya malah kabur dari polisi naik helikopter. Sudah bertahun-tahun tetap seru.'],
            [2, 'Cyberpunk', 8, 'Kota malamnya bikin betah keliling tanpa tujuan. Sesekali masih ada bug lucu, mobil terbang tiba-tiba.'],
            [2, 'Counter-Strike', 8, 'Sekali headshot rasanya kayak menang lotre. Sekali kena AWP, rasanya pengin uninstall.'],
            [2, 'Apex', 7, 'Ping selalu jadi tersangka kalau kalah. Tapi movement-nya memang nggak ada tandingan.'],
            [2, '007', 9, 'Adegan aksinya mewah, bikin pengin pakai jas ke warung. Cocok buat yang suka gaya.'],

            [3, 'Baldur', 10, 'Lempar dadu gagal kritis, malah jadi cerita paling kocak. Setiap pilihan ada akibatnya, jadi jangan asal pencet.'],
            [3, 'Dota', 6, 'Tutorialnya aja sudah kayak kuliah. Lima menit main, sepuluh menit dimaki. Tapi tetap balik lagi.'],
            [3, 'Silent Hill', 9, 'Main malam-malam sendirian, sekarang tidur harus nyalain lampu. Seram dan indah sekaligus.'],
            [3, 'Grand Theft', 10, 'Tiga karakter, tiga kekacauan. Online-nya juga selalu ada hal absurd yang bikin ngakak.'],
            [3, 'Marvel Rivals', 9, 'Pilih healer biar aman, tetap saja disalahkan. Efek kemampuannya meriah banget.'],
            [3, 'AION 2', 8, 'Terbang di langit sambil bertarung itu rasanya beda. Pengin lanjut, tapi tugas kuliah memanggil.'],
            [3, 'Counter-Strike', 7, 'Rasanya seperti ujian tiap ronde: sedikit salah, langsung gugur. Tapi kok ya diulang lagi.'],

            [4, 'EA SPORTS FC', 7, 'Bikin tim impian, tapi yang menang tetap gol dari tendangan acak. Stik hampir melayang.'],
            [4, 'PUBG', 8, 'Mendarat di tempat ramai, langsung jadi penonton. Tapi pas menang makan ayam, rasanya luar biasa.'],
            [4, 'Call of Duty', 7, 'Kampanyenya sinematik banget, multiplayer-nya penuh orang yang jauh lebih jago. Tetap seru kalau main bareng teman.'],
            [4, 'Baldur', 10, 'Niat jadi pahlawan, tapi tiap pilihan ngaco malah paling seru. Satu playthrough tidak akan cukup.'],
            [4, 'Dota', 7, 'Kurva belajarnya setinggi gunung. Tapi pas gank sukses, rasanya nikmat banget.'],
            [4, 'Apex', 8, 'Pas squad kompak, rasanya jadi tim profesional. Pas nggak kompak, jadi grup lawak.'],
            [4, 'Silent Hill', 8, 'Atmosfernya bikin merinding pelan-pelan. Jangan main sambil makan, apalagi sendirian.'],

            [5, '007', 8, 'Rasanya jadi mata-mata keren, padahal ketahuan penjaga terus. Setelan jasnya juara.'],
            [5, 'Dawn of War', 8, 'Untuk Kaisar! Pasukan kecil berubah jadi pasukan besar, lalu hancur lagi. Strateginya bikin deg-degan.'],
            [5, 'AION 2', 7, 'Karakternya bisa dibuat cakep banget, lama-lama sibuk edit karakter daripada main. Grafisnya memanjakan mata.'],
            [5, 'Cyberpunk', 9, 'Ceritanya dalam, gayanya keren. Setelah update, rasanya seperti game yang benar-benar baru.'],
            [5, 'Counter-Strike', 8, 'Tidak butuh grafis bagus buat seru. Cukup lima orang teman dan koneksi yang stabil.'],
            [5, 'EA SPORTS FC', 6, 'Gol-golnya cakep, tapi pemain andalan selalu cedera pas pertandingan penting. Tipikal bola.'],
            [5, 'Call of Duty', 8, 'Misinya tegang, ledakannya meriah. Cocok buat melepas stres sehabis kerja.'],
        ];

        $extraLibrary = [
            1 => ['Grand Theft', 'Silent Hill'],
            2 => ['Baldur', 'PUBG'],
            3 => ['Cyberpunk', 'Red Dead'],
            4 => ['Counter-Strike', 'Marvel Rivals'],
            5 => ['Red Dead', 'Dota'],
        ];

        foreach ($reviews as [$i, $keyword, $rating, $body]) {
            $game = Game::where('title', 'like', "%{$keyword}%")->first();

            if ($game) {
                Review::updateOrCreate(
                    ['user_id' => $users[$i]->id, 'game_id' => $game->id],
                    ['rating' => $rating, 'body' => $body]
                );
                $users[$i]->library()->syncWithoutDetaching([$game->id]);
            }
        }

        foreach ($extraLibrary as $i => $keywords) {
            foreach ($keywords as $keyword) {
                $game = Game::where('title', 'like', "%{$keyword}%")->first();

                if ($game) {
                    $users[$i]->library()->syncWithoutDetaching([$game->id]);
                }
            }
        }
    }
}