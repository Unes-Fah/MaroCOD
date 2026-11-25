<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $default = config('database.default');
        $database = config("database.connections.{$default}.database");

        if ($database !== 'marocod_test') {
            throw new \RuntimeException(
                "Refusing to run tests against '{$database}'. Expected 'marocod_test'."
            );
        }
    }
}