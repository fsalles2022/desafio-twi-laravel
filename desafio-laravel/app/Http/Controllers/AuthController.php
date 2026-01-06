<?php

namespace App\Http\Controllers;


use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['image'] = $request->file('image');

        $result = $this->authService->register($data);

        return response()->json($result, 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        return response()->json(
            $this->authService->login(
                $request->email,
                $request->password
            )
        );
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->noContent(); // 204
    }


    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        return response()->json(
            $this->authService->refresh($request->refresh_token)
        );
    }
}
