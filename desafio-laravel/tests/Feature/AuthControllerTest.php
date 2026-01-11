<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Traits\CreatesRoles;

    public function setUp(): void
    {
        parent::setUp();

        // cria roles necessárias
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'sanctum']);
    }

    /** @test */
    /** @test */
    public function usuario_consegue_se_registrar()
    {
        Role::create([
            'name' => 'student',
            'guard_name' => 'api',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'João Teste',
            'email' => 'joao@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'user',
                'token',
                'refresh_token',
            ]);
    }


    /** @test */
    public function usuario_consegue_logar()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'roles'],
                'token',
                'refresh_token',
            ]);
    }

    /** @test */
    public function usuario_autenticado_consegue_fazer_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        RefreshToken::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(204);

        $this->assertDatabaseMissing('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }
}
