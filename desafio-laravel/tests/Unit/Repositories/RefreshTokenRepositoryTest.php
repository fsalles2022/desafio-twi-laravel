<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\User;
use App\Models\RefreshToken;
use App\Repositories\Eloquent\RefreshTokenRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class RefreshTokenRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected RefreshTokenRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new RefreshTokenRepository();
    }

    /** @test */
    public function cria_um_refresh_token_para_o_usuario()
    {
        $user = User::factory()->create();

        $refreshToken = $this->repository->create($user);

        $this->assertInstanceOf(RefreshToken::class, $refreshToken);
        $this->assertEquals($user->id, $refreshToken->user_id);
        $this->assertNotEmpty($refreshToken->token);
        $this->assertNotNull($refreshToken->expires_at);
    }

    /** @test */
    public function encontra_um_token_valido()
    {
        Carbon::setTestNow(now());

        $user = User::factory()->create();

        $refreshToken = $this->repository->create($user);

        $found = $this->repository->findValid($refreshToken->token);

        $this->assertNotNull($found);
        $this->assertEquals($refreshToken->id, $found->id);
    }

    /** @test */
    public function nao_retorna_token_expirado()
    {
        Carbon::setTestNow(now()->subDays(10));

        $user = User::factory()->create();

        $refreshToken = $this->repository->create($user);

        Carbon::setTestNow(now()->addDays(10));

        $found = $this->repository->findValid($refreshToken->token);

        $this->assertNull($found);
    }

    /** @test */
    public function remove_todos_os_tokens_do_usuario()
    {
        $user = User::factory()->create();

        $this->repository->create($user);
        $this->repository->create($user);

        $this->repository->deleteByUser($user);

        $this->assertDatabaseMissing('refresh_tokens', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function remove_um_token_especifico()
    {
        $user = User::factory()->create();

        $refreshToken = $this->repository->create($user);

        $this->repository->delete($refreshToken);

        $this->assertDatabaseMissing('refresh_tokens', [
            'id' => $refreshToken->id,
        ]);
    }
}
