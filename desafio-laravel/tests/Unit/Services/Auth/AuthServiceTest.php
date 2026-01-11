<?php

namespace Tests\Unit\Services\Auth;

use Tests\TestCase;
use App\Services\Auth\AuthService;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\Traits\CreatesRoles;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRoles;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRoles();

        // Cria a role padrão usada no AuthService
        Role::create([
            'name' => 'student',
            'guard_name' => 'api',
        ]);

        $this->authService = app(AuthService::class);
    }


    /** @test */
    public function usuario_consegue_logar_com_credenciais_validas()
    {
        $password = '123456';

        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->authService->login(
            $user->email,
            $password
        );

        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('refresh_token', $response);

        $this->assertEquals($user->email, $response['user']['email']);

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function login_com_senha_invalida_dispara_excecao()
    {
        $user = User::factory()->create([
            'password' => Hash::make('senha_correta'),
        ]);

        $this->expectException(ValidationException::class);

        $this->authService->login(
            $user->email,
            'senha_errada'
        );
    }

    /** @test */
    public function usuario_consegue_se_registrar_com_sucesso()
    {
        $data = [
            'name' => 'Fabio Salles',
            'email' => 'fabio@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
        ];

        $response = $this->authService->register($data);

        $this->assertArrayHasKey('user', $response);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('refresh_token', $response);

        $this->assertDatabaseHas('users', [
            'email' => 'fabio@example.com',
        ]);

        $user = User::where('email', 'fabio@example.com')->first();

        $this->assertTrue(
            $user->hasRole('student'),
            'Usuário não recebeu a role student'
        );

        $this->assertDatabaseHas('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function usuario_consegue_fazer_logout()
    {
        $user = User::factory()->create();

        // cria token (sanctum)
        $user->createToken('api-token');

        // cria refresh token
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => 'fake-refresh-token',
            'expires_at' => now()->addDays(7),
        ]);

        $this->authService->logout($user);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }
}
