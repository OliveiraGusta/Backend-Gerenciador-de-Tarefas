<?php 

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase {
    use RefreshDatabase; 

    /** @test */
    public function it_can_create_a_user() {
        $userData = [
            'name' => 'Test 1',
            'email' => 'Test@example.com',
            'password' => 'password123',
            'githubUsername' => 'testuser',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User created successfully',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'Test@example.com',
        ]);
    }

    /** @test */
    public function it_can_get_user_by_id() {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'githubUsername' => $user->githubUsername,
                ]);
    }

    /** @test */
    public function it_can_update_a_user() {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'githubUsername' => 'Updated Github',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/users/{$user->id}", $updatedData);

      
        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User updated successfully',
                ]);

       
        $this->assertDatabaseHas('users', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'githubUsername' => 'Updated Github',
        ]);
    }

    /** @test */
    public function it_can_delete_a_user() {
        // Cria o usuário
        $user = User::factory()->create();

        // Cria o token do usuário
        $token = $user->createToken('Test Token')->plainTextToken;

        // Realiza a requisição DELETE para excluir o usuário com o token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/users/{$user->id}");

        // Verifica se a resposta tem o status correto
        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User deleted successfully',
                ]);

        // Verifica se o usuário foi deletado do banco de dados
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function it_cannot_update_another_user() {
        // Tenta atualizar o user2 com o token do user1 

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token = $user1->createToken('Test Token')->plainTextToken;

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'githubUsername' => 'Updated Github',
        ];

    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/users/{$user2->id}", $updatedData);

        $response->assertStatus(403)
                ->assertJson([
                    'error' => 'Unauthorized',
                ]);
    }
    
    /** @test */
    public function it_cannot_delete_another_user() {
        // Tenta deletar o user2 com o token do user1 

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token = $user1->createToken('Test Token')->plainTextToken;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/users/{$user2->id}");

        $response->assertStatus(403)
                ->assertJson([
                    'error' => 'Unauthorized',
                ]);
    }

    /** @test */
    public function it_can_update_its_own_user() {

        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'githubUsername' => 'Updated Github',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/users/{$user->id}", $updatedData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User updated successfully',
                ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'githubUsername' => 'Updated Github',
        ]);
    }

    /** @test */
    public function it_can_delete_its_own_user() {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User deleted successfully',
                ]);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

}

