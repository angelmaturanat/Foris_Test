<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RunForisTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_console_command_run()
    {
        $this->artisan('foris:run students_data_testing.txt')
        ->expectsOutput('Marco: 142 minutes in 2 days.')
        ->assertExitCode(0);
    }
}
