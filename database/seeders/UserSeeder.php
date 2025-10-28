<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy role admin
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Tạo tài khoản admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Tạo tài khoản customer mẫu
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Test',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => $customerRole->id,
            ]
        );
    }
}
