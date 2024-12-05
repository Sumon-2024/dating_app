<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'mobile_number' => '0123456789',
            // 'role' => 'super_admin',
            'password' => Hash::make('123456789'),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'mobile_number' => '0987654321',
            // 'role' => 'admin',
            'password' => Hash::make('123456789'),
        ]);

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'mobile_number' => fake()->phoneNumber(),
                // 'role' => 'user',
                'password' => Hash::make('123456789'),
            ]);
        }
    }
}
