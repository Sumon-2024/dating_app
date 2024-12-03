<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\RoleType;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $superAdmin = Role::create(['name' => RoleType::SUPER_ADMIN]);
        $admin = Role::create(['name' => RoleType::ADMIN]);
        $moderator = Role::create(['name' => RoleType::MODERATOR]);
        $user = Role::create(['name' => RoleType::USER]);

        // Create permissions
        $permissions = [
            'role.index',
            'role.create',
            'role.edit',
            'role.update',
            'role.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'role.index',
            'role.create',
            'role.edit',
            'role.update',
            'role.delete',
        ]);

        $moderator->givePermissionTo([
            'role.index',
            'role.edit',
        ]);

        $user->givePermissionTo([
            'role.index',
        ]);
    }
}
