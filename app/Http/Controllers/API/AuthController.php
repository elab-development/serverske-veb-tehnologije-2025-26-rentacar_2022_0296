<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:admin,client',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'client',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Korisnik uspešno registrovan.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Pogrešni podaci za prijavu.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Uspešna prijava.',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Uspešna odjava.'
        ], 200);
    }
}
