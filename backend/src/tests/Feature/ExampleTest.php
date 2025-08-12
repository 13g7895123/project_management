<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example - test health endpoint instead of root.
     */
    public function test_api_health_endpoint_returns_successful_response(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200);
    }
}
