<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@gamelibrary.test'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->assignRole($adminRole);

        // Developer role and a demo developer account
        Role::firstOrCreate(['name' => 'developer']);

        $dev = User::firstOrCreate(
            ['email' => 'dev@gamelibrary.test'],
            ['name' => 'Studio Demo', 'password' => Hash::make('password'), 'bio' => 'Akun developer contoh.']
        );
        $dev->assignRole('developer');
    }
}
