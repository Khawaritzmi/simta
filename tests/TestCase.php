<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->forceInMemoryTestingDatabase();
        $this->guardAgainstRealDatabaseRefresh();

        parent::setUp();

        $this->withoutVite();
    }

    private function forceInMemoryTestingDatabase(): void
    {
        foreach ([
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'CACHE_STORE' => 'array',
            'SESSION_DRIVER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
        ] as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    private function guardAgainstRealDatabaseRefresh(): void
    {
        $database = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE');

        if ($database !== ':memory:') {
            throw new \RuntimeException("Tests must use DB_DATABASE=:memory:. Current DB_DATABASE is {$database}.");
        }
    }
}
