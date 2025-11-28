<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTeacherController extends Controller
{
    public function createTeacher(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'image'    => $imagePath,
        ]);
        

        // Atribui a role de teacher
        $user->assignRole('teacher');

        // Carrega as roles
        $user->load('roles');

        return response()->json([
            'user'  => $user,
            'roles' => $user->getRoleNames(),
        ], 201);
    }
}
