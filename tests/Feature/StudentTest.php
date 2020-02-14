<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StudentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_console_command_student()
    {
        $this->artisan('foris:Student Testing')
        ->expectsOutput('The student Testing was added successfully.')
        ->assertExitCode(0);
        
        Cache::flush();
    }
}
