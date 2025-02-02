<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create();
    }

    public function testIndex(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
    }

    public function testCreate(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.create'));
        $response->assertOk();
    }

    public function testEdit(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.edit', $this->task));
        $response->assertOk();
    }

    public function testStore(): void
    {
        $data = Task::factory()->make()->only(['name', 'description', 'status_id', 'assigned_to_id']);
        $response = $this->actingAs($this->user)->post(route('tasks.store', $data));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $data);
    }

    public function testUpdate(): void
    {
        $data = Task::factory()->make()->only(['name', 'description', 'status_id', 'assigned_to_id']);
        $response = $this->actingAs($this->user)->patch(route('tasks.update', $this->task), $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $data);
    }

    public function testShow(): void
    {
        $response = $this->get(route('tasks.show', $this->task));
        $response->assertOk();
    }

    public function testDelete(): void
    {
        $task = Task::factory()->create([
            'created_by_id' => $this->user->id
        ]);
        $response = $this->actingAs($this->user)->delete(route('tasks.destroy', $task));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseMissing('tasks', ['id' => (array) $task['id']]);
    }
}
