<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_consegue_logar(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('123456'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);

        $response->assertStatus(200);
    }
}
