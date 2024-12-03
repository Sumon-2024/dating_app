<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'mobile_number' => '1234567890',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('123456789'),
            'remember_token' => Str::random(10),
        ]);
        $superAdmin->assignRole(RoleType::SUPER_ADMIN);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'mobile_number' => '0987654321',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('123456789'),
            'remember_token' => Str::random(10),
        ]);
        $admin->assignRole(RoleType::ADMIN);

        $moderator = User::create([
            'name' => 'Moderator',
            'email' => 'moderator@gmail.com',
            'mobile_number' => '1122334455',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('123456789'),
            'remember_token' => Str::random(10),
        ]);
        $moderator->assignRole(RoleType::MODERATOR);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'mobile_number' => '5566778899',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('123456789'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole(RoleType::USER);
    }
}
