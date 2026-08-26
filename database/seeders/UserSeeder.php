<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@rayatender.id'],
            [
                'name'     => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('Admin@12345'),
                'status'   => 'active',
            ]
        );
        $admin->assignRole('Super Admin');

        // Marketing Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@rayatender.id'],
            [
                'name'     => 'Marketing Manager',
                'username' => 'manager',
                'password' => Hash::make('Manager@12345'),
                'status'   => 'active',
            ]
        );
        $manager->assignRole('Marketing Manager');

        // Marketing Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@rayatender.id'],
            [
                'name'     => 'Marketing Staff',
                'username' => 'staff',
                'password' => Hash::make('Staff@12345'),
                'status'   => 'active',
            ]
        );
        $staff->assignRole('Marketing Staff');
    }
}
