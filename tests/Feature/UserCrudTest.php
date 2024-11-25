<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    // Teste de registro de usuário
    public function test_register_user()
    {
        $data = [
            'name' => 'Teste User',
            'email' => 'teste@example.com',
            'password' => 'teste123',
            'githubUsername' => 'teste',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'User created successfully',
        ]);
        $this->assertDatabaseHas('users', ['email' => 'teste@example.com']);
    }

    public function test_login_user()
    {
        $user = User::factory()->create([
            'email' => 'teste@example.com',
            'password' => Hash::make('teste123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste@example.com',
            'password' => 'teste123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_show_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'githubUsername' => $user->githubUsername,
        ]);
    }

    public function test_update_user()
    {
        $user = User::factory()->create();

        $data = ['githubUsername' => 'updatedUsername'];

        $response = $this->actingAs($user)->putJson('/api/user', $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User updated successfully']);
        $this->assertDatabaseHas('users', ['githubUsername' => 'updatedUsername']);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User deleted successfully']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_get_authenticated_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'githubUsername' => $user->githubUsername,
        ]);
    }
}
