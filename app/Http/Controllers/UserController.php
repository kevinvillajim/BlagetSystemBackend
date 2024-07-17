<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            // otros campos de validación
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ci' => $request->ci,
            'phone' => $request->phone,
            // otros campos
        ]);

        return response()->json($user, 201);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'active' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);
        $user->active = $request->active;
        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo eliminar el usuario'], 500);
        }
    }
}
