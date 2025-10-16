<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Video;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Usuários ---
        $users = [
            ['name' => 'Fabio', 'email' => 'fabio@email.com', 'password' => bcrypt('123456')],
            ['name' => 'Alice', 'email' => 'alice@email.com', 'password' => bcrypt('123456')],
            ['name' => 'Bob', 'email' => 'bob@email.com', 'password' => bcrypt('123456')],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }

        // --- Vídeos (do Node.js) ---
        $videos = [
            ['title' => 'Introdução', 'filename' => 'intro.mp4'],
            ['title' => 'Aula 1', 'filename' => 'aula1.mp4'],
            ['title' => 'Aula 2', 'filename' => 'aula2.mp4'],
        ];

        foreach ($videos as $data) {
            Video::updateOrCreate(['filename' => $data['filename']], $data);
        }

        // --- Associar vídeos aos usuários ---
        $allUsers = User::all();
        $allVideos = Video::all();

        foreach ($allUsers as $user) {
            foreach ($allVideos as $video) {
                // Marca como assistido aleatoriamente
                $watched = rand(0, 1) === 1;
                $user->videos()->syncWithoutDetaching([
                    $video->id => ['watched' => $watched]
                ]);
            }
        }
    }
}
