<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role ada
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Buat Admin dummy
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole($adminRole);

        // Buat User dummy
        $user = User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'User',
                'username' => 'user',
                'password' => Hash::make('user123'),
            ]
        );
        $user->assignRole($userRole);
    }
}
