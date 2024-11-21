<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase; // Garante que o banco é limpo antes de cada teste.

    public function test_user_can_be_created()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'githubUsername' => 'testuser',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User created successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}
