<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'client',
        ]);

        // Automatska prijava korisnika u sesiju nakon registracije
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Korisnik uspešno registrovan i prijavljen u sesiju.',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Pogrešni podaci za prijavu.'
            ], 401);
        }

        // Regenerisanje ID-a sesije nakon uspešne prijave
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Uspešna prijava u sesiju.',
            'user' => Auth::user()
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // Poništavanje sesije i osvežavanje CSRF tokena
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Uspešna odjava iz sesije.'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => Auth::user()
        ], 200);
    }
}
