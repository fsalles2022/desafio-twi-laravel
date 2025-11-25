<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Registro de usuário
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        // Define a role padrão
        $role = 'student';

        // Salva imagem se enviada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        // Cria o usuário
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'image'    => $imagePath,
        ]);

        // Atribui role padrão
        $user->assignRole($role);

        // Tokens
        $token = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $this->createRefreshToken($user);

        return response()->json([
            'user'          => $user,
            'token'         => $token,
            'refresh_token' => $refreshToken->token,
        ], 201);
    }


    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        // Remove refresh tokens antigos
        RefreshToken::where('user_id', $user->id)->delete();

        $token = $user->createToken('api-token')->plainTextToken;
        $refreshToken = $this->createRefreshToken($user);

        return response()->json([
            'user'          => $user,
            'token'         => $token,
            'refresh_token' => $refreshToken->token,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        // Deleta todos refresh tokens do usuário
        RefreshToken::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    // Refresh token
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $refreshToken = RefreshToken::where('token', $request->refresh_token)->first();

        if (! $refreshToken || $refreshToken->expires_at->isPast()) {
            return response()->json(['error' => 'Refresh token inválido ou expirado'], 401);
        }

        $user = $refreshToken->user;

        // Remove refresh token usado e gera novo
        $refreshToken->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        $newRefresh = $this->createRefreshToken($user);

        return response()->json([
            'user'          => $user,
            'token'         => $token,
            'refresh_token' => $newRefresh->token,
        ]);
    }

    // Cria refresh token
    private function createRefreshToken(User $user)
    {
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addDays(7);

        return RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);
    }
}
