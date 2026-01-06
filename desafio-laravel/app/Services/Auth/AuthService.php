<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {}

    public function register(array $data)
    {
        $role = 'student';

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
        $refreshToken = $this->refreshTokenRepository->create($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken->token,
        ];
    }

    public function login(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $this->refreshTokenRepository->deleteByUser($user);

        $token = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $this->refreshTokenRepository->create($user);

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
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        $this->refreshTokenRepository->deleteByUser($user);
    }

    public function refresh(string $refreshToken)
    {
        $refresh = $this->refreshTokenRepository->findValid($refreshToken);

        if (! $refresh) {
            abort(401, 'Refresh token inválido ou expirado');
        }

        $user = $refresh->user;

        $this->refreshTokenRepository->delete($refresh);

        $token = $user->createToken('api-token')->plainTextToken;
        $newRefresh = $this->refreshTokenRepository->create($user);

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $newRefresh->token,
        ];
    }
}
