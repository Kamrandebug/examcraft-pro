<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@examcraft.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        Role::updateOrCreate(
            ['user_id' => $admin->id],
            ['name' => 'admin']
        );

        // Seed a regular user for testing
        $user = User::updateOrCreate(
            ['email' => 'user@examcraft.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('user123'),
            ]
        );

        Role::updateOrCreate(
            ['user_id' => $user->id],
            ['name' => 'user']
        );
    }
}
