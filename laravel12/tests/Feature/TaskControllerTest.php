<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_forbidden_for_unauthenticated_task_requests(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthenticated']);
    }
}
