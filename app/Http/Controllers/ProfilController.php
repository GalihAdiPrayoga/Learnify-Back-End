<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'status' => true,
            'message' => 'List semua user',
            'data' => $users
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {

            // Upload foto jika ada
            if ($request->hasFile('profile')) {
                $data['profile'] = $request->file('profile')->store('profiles', 'public');
            }

            // Hash password
            $data['password'] = Hash::make($data['password']);

            // Buat user baru
            $user = User::create($data);

            // Assign role jika diperlukan
            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            } else {
                $user->assignRole('User');
            }

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dibuat',
                'data' => $user
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $user = User::with('roles')->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail user',
            'data' => $user
        ]);
    }

    /**
     * Show authenticated user's own profile
     */
    public function showOwn()
    {
        $user = Auth::user()->load('roles');

        return response()->json([
            'status' => true,
            'message' => 'Profil user',
            'data' => $user
        ]);
    }

    /**
     * Update authenticated user's own profile
     */
    public function updateOwn(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // User tidak bisa ubah role sendiri
        unset($data['role']);

        return DB::transaction(function () use ($user, $data, $request) {

            // Upload foto baru
            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                    Storage::disk('public')->delete($user->profile);
                }
                $data['profile'] = $request->file('profile')->store('profiles', 'public');
            }

            // Hash password baru
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Update user
            $user->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $user->fresh()->load('roles')
            ]);
        });
    }

    public function update(UpdateProfileRequest $request, string $id)
    {
           $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $data = $request->validated();

        return DB::transaction(function () use ($user, $data, $request) {

            // kalau ada foto baru
            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                    Storage::disk('public')->delete($user->profile);
                }
                $data['profile'] = $request->file('profile')->store('profiles', 'public');
            }

            // password baru
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // update user
            $user->update($data);

            // update role
            if (!empty($data['role'])) {
                $user->syncRoles($data['role']);
            }

            return response()->json([
                'status' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
     $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return DB::transaction(function () use ($user) {

            // hapus foto
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }

            // hapus user
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus'
            ]);
        });
    }
}
