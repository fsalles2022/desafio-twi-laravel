<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Listar todos os usuários com vídeos
     */
    public function index()
    {
        $users = User::with('videos')->get();
        return response()->json($users);
    }

    /**
     * Mostrar um usuário específico com vídeos
     */
    public function show($id)
    {
        $user = User::with('videos')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Criar um novo usuário
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'image' => 'nullable',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json($user, 201);
    }

    /**
     * Atualizar um usuário
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:6',
            'image' => 'nullable',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * Deletar um usuário
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuário deletado']);
    }

    /**
     * Endpoint opcional: listar vídeos de um usuário específico
     */
    public function videos($id)
    {
        $user = User::with('videos')->findOrFail($id);
        return response()->json($user->videos);
    }

    // Pega dados do usuário logado
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    // Atualiza dados do usuário
    // public function updateProfile(Request $request)
    // {

    //     $user = $request->user();
    //     $data = $request->validate([
    //         'name'  => 'sometimes|string|max:255',
    //         'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
    //         'image' => 'nullable|image|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         $path = $request->file('image')->store('users', 'public');
    //         $data['image'] = $path;
    //     }

    //     $user->update($data);

    //     return response()->json($user);
    // }
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }
}
