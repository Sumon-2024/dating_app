<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permission1 = Permission::create(['name' => 'view dashboard']);
        $permission2 = Permission::create(['name' => 'edit user']);
        $permission3 = Permission::create(['name' => 'delete user']);
        $permission4 = Permission::create(['name' => 'create post']);
        $permission5 = Permission::create(['name' => 'edit post']);
        $permission6 = Permission::create(['name' => 'delete post']);
        $permission7 = Permission::create(['name' => 'view post']);

        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());
        $admin->givePermissionTo([$permission1, $permission2, $permission4, $permission5, $permission7]);
        $user->givePermissionTo([$permission1, $permission7]);

        // Assign roles to users
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
