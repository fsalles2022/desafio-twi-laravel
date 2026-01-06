<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\RefreshToken;

interface RefreshTokenRepositoryInterface
{
    public function create(User $user): RefreshToken;

    public function findValid(string $token): ?RefreshToken;

    public function deleteByUser(User $user): void;

    public function delete(RefreshToken $refreshToken): void;
}
