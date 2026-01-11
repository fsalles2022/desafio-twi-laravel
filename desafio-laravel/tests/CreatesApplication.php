<?php

namespace Tests;

use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Cria a aplicação Laravel para testes.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
