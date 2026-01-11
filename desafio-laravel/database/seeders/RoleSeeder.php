<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'teacher', 'guard_name' => 'sanctum'],
            ['name' => 'student', 'guard_name' => 'sanctum'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
