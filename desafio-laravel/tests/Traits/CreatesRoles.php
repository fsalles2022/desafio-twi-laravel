<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait CreatesRoles
{
    protected function createRoles(): void
    {
        // Limpa cache do Spatie (obrigatório)
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['admin', 'teacher', 'student'] as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'sanctum',
            ]);
        }

        // Garante que o cache foi reconstruído
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
