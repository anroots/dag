<?php

namespace App\Test;

use App\Db\Organization;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use DatabaseMigrations;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @var Organization
     */
    protected $organization;

    /**
     * @var Generator
     */
    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->organization = new Organization;
        $this->faker = Factory::create();
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
