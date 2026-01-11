<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Role;

trait AutoSeedsRoles
{
    /**
     * Executa antes de cada teste e garante que todas as roles existem.
     */
    protected function setUpRoles(): void
    {
        $roles = [
            ['name' => 'teacher', 'guard_name' => 'sanctum'],
            ['name' => 'student', 'guard_name' => 'sanctum'],
            // Adicione outras roles que precisar
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name'], 'guard_name' => $role['guard_name']]);
        }
    }
}
