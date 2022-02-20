<?php

use Niece1\Labels\LabelsServiceProvider;

/**
 * Description of TestCase
 *
 * @author test
 */
abstract class TestCase extends Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Eloquent::unguard();
        $this->artisan('migrate', [
            '--database' => 'sqlite',
            '--realpath' => realpath(__DIR__ . '/../database/migrations'),
        ]);
    }

    public function tearDown(): void
    {
        \Schema::drop('books');
    }

    protected function getPackageProviders($app)
    {
        return [LabelsServiceProvider::class];
    }

    protected function getEnvironmentSetup($app)
    {
        $app['config']->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        \Schema::create('books', function ($table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }
}
