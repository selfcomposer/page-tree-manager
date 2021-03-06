<?php

namespace SelfComposer\PageTreeManager\Tests;

use SelfComposer\PageTreeManager\Tests\TestCase;

class GenericTestCase extends TestCase
{
	/**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('page-tree-manager', include(__DIR__ . '/config/config.php'));

        // set up database configuration
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}