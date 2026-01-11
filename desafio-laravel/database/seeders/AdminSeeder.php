<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name' => 'Admin',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->assignRole($role);
    }
}
