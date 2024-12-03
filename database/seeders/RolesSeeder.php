<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Assign roles to existing users
        $superAdminUser = User::find(1);
        if ($superAdminUser) {
            $superAdminUser->assignRole('super-admin');
        }

        $adminUser = User::find(2);
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }

        $regularUser = User::find(3);
        if ($regularUser) {
            $regularUser->assignRole('user');
        }
    }
}
