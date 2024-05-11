<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Task::factory(10)->create();
    }

    public function test_renders_table_of_tasks(): void
    {
        $response = $this->get('/tasks');
        $response->assertViewIs('tasks.index');
        $response->assertViewHas('tasks', Task::all());
        $response->assertOk();
    }

    public function test_redirect_to_all_tasks_on_invalid_task()
    {

        $lastTask = Task::all()->last();

        $response = $this->get('/tasks/'.$lastTask->id + 1);
        $response->assertRedirectToRoute('task.index', [
            'error' => 'Requested resource does not exist.',
        ]);
        $response = $this->get('/tasks/'.($lastTask->id + 1).'/edit');
        $response->assertRedirectToRoute('task.index', [
            'error' => 'Requested resource does not exist.',
        ]);
        $response = $this->get('/tasks/'.($lastTask->id + 1).'/delete');
        $response->assertRedirectToRoute('task.index', [
            'error' => 'Requested resource does not exist.',
        ]);

    }

    public function test_show_valid_task_data()
    {
        $firsTask = Task::all()->first();
        $lastTask = Task::all()->last();

        $task = Task::whereId(random_int($firsTask->id, $lastTask->id))->first();

        $response = $this->get('/tasks/'.$task->id);
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
    }

    public function test_create_task_get_method_returns_view()
    {
        $response = $this->get('/tasks/create');
        $response->assertOk();
        $response->assertViewIs('tasks.create');

    }

    public function test_create_task_does_not_work_with_invalid_data()
    {
        $response = $this->post('/tasks/create', []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $response = $this->post('/tasks/create', [
            'title' => fake()->regexify('/{A-Za-z0-9}{300}/'),
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 256 characters.',
        ]);
    }

    public function test_create_task_works_with_valid_data()
    {
        $taskPayload = [
            'title' => fake()->text(120),
            'description' => fake()->text(),
        ];
        $taskCount = Task::all()->count();
        $response = $this->post('/tasks/create', $taskPayload);
        $response->assertRedirectToRoute('task.index', [
            'message' => 'New task has been created.',
        ]);
        $this->assertDatabaseCount('tasks', $taskCount + 1);
        $this->assertDatabaseHas('tasks', $taskPayload);
    }

    public function test_delete_task_get_method_returns_view()
    {
        $firstTask = Task::all()->first();
        $lastTask = Task::all()->last();

        $task = Task::whereId(random_int($firstTask->id, $lastTask->id))->first();

        $response = $this->get('/tasks/'.$task->id.'/delete');
        $response->assertOk();
        $response->assertViewIs('tasks.delete');
        $response->assertViewHas('task', $task);

    }

    public function test_delete_task_and_return_to_all_tasks()
    {
        $firstTask = Task::all()->first();
        $lastTask = Task::all()->last();

        $task = Task::whereId(random_int($firstTask->id, $lastTask->id))->first();

        $response = $this->delete('/tasks/'.$task->id.'/delete');
        $response->assertRedirectToRoute('task.index', [
            'message' => 'Task is deleted.',
        ]);
    }

    public function test_edit_task_get_method_returns_view()
    {
        $firstTask = Task::all()->first();
        $lastTask = Task::all()->last();

        $task = Task::whereId(random_int($firstTask->id, $lastTask->id))->first();

        $response = $this->get('/tasks/'.$task->id.'/delete');
        $response->assertOk();
        $response->assertViewIs('tasks.delete');
        $response->assertViewHas('task', $task);
    }

    public function test_edit_task_updates_the_task_and_return_to_show_task()
    {
        $firstTask = Task::all()->first();
        $lastTask = Task::all()->last();

        $task = Task::whereId(random_int($firstTask->id, $lastTask->id))->first();

        $validPayload = [
            'status' => fake()->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => fake()->title(),
            'description' => fake()->text(),
        ];

        $response = $this->put('/tasks/'.$task->id.'/edit', $validPayload);
        $response->assertRedirectToRoute('task.show', [
            'task' => $task,
        ]);
    }

    public function test_edit_task_throw_error_on_invalid_title()
    {
        $firstTask = Task::all()->where('status', '!=', TaskStatus::Completed)->first();
        $lastTask = Task::all()->where('status', '!=', TaskStatus::Completed)->last();

        $task = Task::whereId(random_int($firstTask->id, $lastTask->id))->first();

        $response = $this->put('/tasks/'.$task->id.'/edit');
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->put('/tasks/'.$task->id.'/edit', [
            'title' => fake()->title(),
            'description' => fake()->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $response = $this->put('/tasks/'.$task->id.'/edit', [
            'title' => fake()->title(),
            'description' => fake()->text(),
            'status' => 'completed',
        ]);
        $response = $this->put('/tasks/'.$task->id.'/edit', [
            'title' => fake()->title(),
            'description' => fake()->text(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'Changing task status is not allowed once it is marked as completed.',
        ]);
    }
}
