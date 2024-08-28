<?php

namespace Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        if (!file_exists(config('database.connections.sqlite.database'))) {
            touch(config('database.connections.sqlite.database'));
        }
        return $app;
    }

}
