<?php

namespace SelfComposer\PageTreeManager\Tests;

use Illuminate\Support\Facades\DB;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    // Default language for test pages
    protected $lang = 'en';

	public function setUp()
	{
		parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'test',
            '--realpath' => realpath(__DIR__ . '/migrators'),
        ]);

        DB::statement('PRAGMA foreign_keys = ON');
	}

	/**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \SelfComposer\PageTreeManager\Providers\ServiceProvider::class
        ];
    }
}