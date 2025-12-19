<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CreateTeacherController extends Controller
{
    public function createTeacher(Request $request)
    {
        // 🔒 Garantia de segurança (caso a rota falhe)
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acesso não autorizado');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        // Upload da imagem
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        // Cria o professor
        $teacher = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'image'    => $imagePath,
        ]);

        // Atribui role
        $teacher->assignRole('teacher');

        return response()->json([
            'user'  => $teacher,
            'roles' => $teacher->getRoleNames(),
        ], 201);
    }

    public function index()
    {
        // 🔒 Proteção extra
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acesso não autorizado');
        }

        return User::role('teacher')->get();
    }
}
