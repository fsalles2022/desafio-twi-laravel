<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthRefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_consegue_renovar_token_com_refresh_token_valido()
    {
        // Arrange: cria usuário
        $user = User::factory()->create();

        // Cria refresh token válido
        $refreshToken = RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(60),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        // Act: chama endpoint de refresh
        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken->token,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'token',
                'refresh_token',
            ]);

        // Token antigo deve ser removido
        $this->assertDatabaseMissing('refresh_tokens', [
            'id' => $refreshToken->id,
        ]);

        // Novo token deve existir
        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function nao_permite_refresh_com_token_invalido()
    {
        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'token-invalido',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function nao_permite_refresh_com_token_expirado()
    {
        $user = User::factory()->create();

        $refreshToken = RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(60),
            'expires_at' => Carbon::now()->subDay(), // expirado
        ]);

        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken->token,
        ]);

        $response->assertStatus(401);
    }
}
