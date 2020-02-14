<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PresenceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_console_command_presence()
    {
        $this->artisan('foris:Student Testing')
        ->expectsOutput('The student Testing was added successfully.')
        ->assertExitCode(0);

        $this->artisan('foris:Presence Testing 1 2020-02-10 09:02 10:17 R100')
        ->expectsOutput('The student presence was added successfully.')
        ->assertExitCode(0);

        Cache::flush();
    }
}
