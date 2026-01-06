<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrasiRequest;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // REGISTER
    public function register(RegistrasiRequest $request)
    {
        $data = $request->validated();

        // Simpan file profile jika ada
        $profilePath = null;
        if ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')->store('profiles', 'public');
        }

        $user = DB::transaction(function () use ($data, $profilePath) {
            // Buat user baru
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'], // password otomatis hashed karena casts di model
                'profile' => $profilePath,
            ]);

            // Assign default role 'User'
            $user->assignRole('User');

            return $user;
        });

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    // LOGIN
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        // Validasi user & password
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // INFO USER (me)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
