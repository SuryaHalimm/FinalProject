<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $adminRole = Role::create([
            'name' => 'admin'
        ]);

        $userRole = Role::create([
            'name' => 'user'
        ]);

        $user = User::create([
            'name' => 'suk',
            'email' => 'suk@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $user->assignRole($adminRole);
    }
}
