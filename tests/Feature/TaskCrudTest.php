<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->task = Task::factory()->create([
            'userOwner' => $this->user->id,
        ]);
    }

    /** @test */
    public function test_index()
    {
        $response = $this->actingAs($this->user)->get('/api/users/' . $this->user->id . '/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'title', 'description', 'status', 'userOwner', 'created_at', 'updated_at']
                 ]);
    }

    /** @test */
    public function test_create()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Description for new task',
            'status' => 0,
        ];

        $response = $this->actingAs($this->user)->post('/api/users/' . $this->user->id . '/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'task' => ['id', 'title', 'description', 'status', 'userOwner', 'created_at', 'updated_at']
                 ]);
    }

    /** @test */
    public function test_get_task()
    {
        $response = $this->actingAs($this->user)
                         ->get('/api/users/' . $this->user->id . '/tasks/' . $this->task->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $this->task->id,
                     'title' => $this->task->title,
                     'description' => $this->task->description,
                     'status' => $this->task->status,
                     'userOwner' => $this->user->id,
                 ]);
    }

    /** @test */
    public function test_update_task()
    {
        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'status' => 2,
        ];

        $response = $this->actingAs($this->user)
                         ->put('/api/users/' . $this->user->id . '/tasks/' . $this->task->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Task updated successfully',
                     'task' => [
                         'id' => $this->task->id,
                         'title' => 'Updated Task Title',
                         'description' => 'Updated description',
                         'status' => 2,
                     ]
                 ]);
    }

    /** @test */
    public function test_delete_task()
    {
        $response = $this->actingAs($this->user)
                         ->delete('/api/users/' . $this->user->id . '/tasks/' . $this->task->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Task deleted successfully',
                 ]);

        $this->assertDatabaseMissing('tasks', ['id' => $this->task->id]);
    }
}
