<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\LoyaltyTier;
use App\Models\UserLoyaltyPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy role admin
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // Tạo tài khoản admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('123123'),
                'role_id' => $adminRole->id,
            ]
        );

        // Tạo tài khoản customer mẫu
        $customer = User::updateOrCreate(
            ['email' => 'hieult2610@gmail.com'],
            [
                'name' => 'Hieu Le',
                'email' => 'hieult2610@gmail.com',
                'password' => Hash::make('123123'),
                'role_id' => $customerRole->id,
            ]
        );

        // Tạo loyalty points cho customer
        $silverTier = LoyaltyTier::where('name', 'Bạc')->first();
        if ($silverTier) {
            UserLoyaltyPoint::updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'total_points' => 0,
                    'current_tier_id' => $silverTier->id
                ]
            );
        }

        // Tạo tài khoản staff mẫu
        User::updateOrCreate(
            ['email' => 'ngochanghoaiduc@gmail.com'],
            [
                'name' => 'Hang Le Staff',
                'email' => 'ngochanghoaiduc@gmail.com',
                'password' => Hash::make('123123'),
                'role_id' => $staffRole->id,
            ]
        );
    }
}
