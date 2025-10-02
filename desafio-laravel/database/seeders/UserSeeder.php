<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Fabio Salles',
            'email' => 'fabio@example.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
