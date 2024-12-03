<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;  

class UserSeeder extends Seeder
{
    /**
     * @method
     * User Seeder for Admin
     * 
     * @description Seeds the database with an admin user.
     * 
     * @bodyParam name string required Example: admin
     * @bodyParam email string required Example: admin@gmail.com
     * @bodyParam password string required Example: 123456789
     */
    
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'mobile_number' => '0123456789',
            'password' => Hash::make('123456789'), 
        ]);
    }
}
