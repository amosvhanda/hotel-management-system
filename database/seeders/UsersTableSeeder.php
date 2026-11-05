<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Demo accounts for a fresh install. Change these passwords straight after installing.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin', 'status' => 'active']
        );
        $admin->assignRole('Super Admin');

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'Guest User', 'password' => Hash::make('password'), 'role' => 'user', 'status' => 'active']
        );
    }
}
