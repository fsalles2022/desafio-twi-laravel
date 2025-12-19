<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    public function register(array $data)
    {
        // role padrão
        $role = 'student';

        // imagem
        $imagePath = null;
        if (isset($data['image'])) {
            $imagePath = $data['image']->store('users', 'public');
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'image'    => $imagePath,
        ]);

        $user->assignRole($role);

        $token = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function login(string $email, string $password)
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        RefreshToken::where('user_id', $user->id)->delete();

        $token = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $this->createRefreshToken($user);

        return [
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'image' => $user->image,
                'image_url' => $user->image_url,
                'roles' => $user->getRoleNames(),
            ],
            'token' => $token,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function logout(User $user): void
    {
        // Revoga o token atual com segurança
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        RefreshToken::where('user_id', $user->id)->delete();
    }



    public function refresh(string $refreshToken)
    {
        $refresh = RefreshToken::where('token', $refreshToken)->first();

        if (! $refresh || $refresh->expires_at->isPast()) {
            abort(401, 'Refresh token inválido ou expirado');
        }

        $user = $refresh->user;

        $refresh->delete();

        $token = $user->createToken('api-token')->plainTextToken;
        $newRefresh = $this->createRefreshToken($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $newRefresh->token,
        ];
    }

    private function createRefreshToken(User $user)
    {
        return RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(60),
            'expires_at' => Carbon::now()->addDays(7),
        ]);
    }
}
