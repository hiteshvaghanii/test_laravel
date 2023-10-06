<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testUserApi()
    {
        // You can simulate an HTTP request to your endpoint
        $response = $this->get('/api/users');

        // Assert that the response has a 200 status code
        $response->assertStatus(200);

        // Add more assertions as needed based on your function's logic
    }

    public function testActivityApi()
    {
        $response = $this->get('/api/activities');
        $response->assertStatus(200);
    }
}
