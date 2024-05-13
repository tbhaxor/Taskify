<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Group;
use App\Models\Task;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Task::factory(100)->create();
    }

    public function test_redirect_to_all_groups_when_task_group_mismatch()
    {
        $group = Group::all()->shuffle()->first();
        $task = Task::all()->where('group_id', '!=', $group->id)->first();

        $response = $this->get("/groups/{$group->id}/tasks/{$task->id}");
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'error' => 'Requested task does not belong to this group.',
        ]);
    }

    public function test_show_valid_task_data()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->shuffle()->first();

        $response = $this->get("/groups/{$group->id}/tasks/{$task->id}");
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $group);
    }

    public function test_create_task_get_method_returns_view()
    {
        $group = Group::all()->shuffle()->first();

        $response = $this->get("/groups/{$group->id}/tasks/create");
        $response->assertOk();
        $response->assertViewIs('tasks.create');
    }

    public function test_create_task_does_not_work_with_invalid_data()
    {
        $group = Group::all()->shuffle()->first();
        $oldTaskCount = $group->tasks->count();

        $response = $this->post("/groups/{$group->id}/tasks/create", []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $taskPayload = [
            'title' => fake()->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->post("/groups/{$group->id}/tasks/create", $taskPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);

        $this->assertEquals($oldTaskCount, $group->tasks->count());
        $this->assertDatabaseMissing('tasks', $taskPayload);
    }

    public function test_create_task_works_with_valid_data()
    {
        $group = Group::all()->shuffle()->first();

        $taskPayload = [
            'title' => fake()->text(64),
            'description' => fake()->text(),
        ];
        $response = $this->post("/groups/{$group->id}/tasks/create", $taskPayload);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'New task has been created.',
        ]);
        $this->assertDatabaseHas('tasks', $taskPayload);

        $this->assertEquals($group->tasks->last()->status, TaskStatus::Pending);
        $this->assertNull($group->tasks->last()->completed_at);
    }

    public function test_delete_task_get_method_returns_view()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->first();

        $response = $this->get("/groups/{$group->id}/tasks/{$task->id}/delete");
        $response->assertOk();
        $response->assertViewIs('tasks.delete');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $group);
    }

    public function test_delete_task_and_return_to_all_tasks()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->first();

        $response = $this->delete("/groups/{$group->id}/tasks/{$task->id}/delete");
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'Task is deleted.',
        ]);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_edit_task_get_method_returns_view()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->first();

        $response = $this->get("/groups/{$group->id}/tasks/{$task->id}/edit");
        $response->assertOk();
        $response->assertViewIs('tasks.edit');
        $response->assertViewHas('task', $task);
    }

    public function test_edit_task_updates_the_task_and_return_to_show_task()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->first();

        $validPayload = [
            'status' => fake()->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => fake()->text(64),
            'description' => fake()->text(),
        ];

        $response = $this->put("/groups/{$group->id}/tasks/{$task->id}/edit", $validPayload);
        $response->assertRedirectToRoute('task.show', [
            'group' => $group,
            'task' => $task,
        ]);
    }

    public function test_edit_task_throw_errors_on_invalid_payload()
    {
        $group = Group::has('tasks', '>=', 1)->first();
        $task = $group->tasks->where('status', '!=', TaskStatus::Completed)->first();

        $response = $this->put("/groups/{$group->id}/tasks/{$task->id}/edit");
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->put("/groups/{$group->id}/tasks/{$task->id}/edit", [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $response = $this->put("/groups/{$group->id}/tasks/{$task->id}/edit", [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'completed',
        ]);
        $response = $this->put("/groups/{$group->id}/tasks/{$task->id}/edit", [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'Changing task status is not allowed once it is marked as completed.',
        ]);
    }
}
