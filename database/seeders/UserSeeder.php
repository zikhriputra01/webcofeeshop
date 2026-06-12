<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Administrator',
            'username' => 'admin',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        User::create([
            'nama' => 'Kasir Utama',
            'username' => 'kasir',
            'password' => 'password123',
            'role' => 'kasir',
        ]);
    }
}
