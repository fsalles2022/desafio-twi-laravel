<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function create(User $user): RefreshToken
    {
        return RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(60),
            'expires_at' => Carbon::now()->addDays(7),
        ]);
    }

    public function findValid(string $token): ?RefreshToken
    {
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteByUser(User $user): void
    {
        RefreshToken::where('user_id', $user->id)->delete();
    }

    public function delete(RefreshToken $refreshToken): void
    {
        $refreshToken->delete();
    }
}
