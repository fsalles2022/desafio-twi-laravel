<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Listar todos os usuários
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Criar novo usuário
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Mostrar usuário específico
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Deletar usuário
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Retorna 204 (No Content) para operações de delete bem-sucedidas
        return response()->json(null, 204);
    }

    /**
     * Consumir microserviço Node.js
     */
    public function externalService()
    {
        // Simulação de resposta do microserviço
        return response()->json([
            'status' => 'ok',
            'message' => 'Microserviço simulado'
        ]);
    }
}
