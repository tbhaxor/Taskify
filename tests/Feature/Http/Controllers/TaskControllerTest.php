<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_redirect_to_all_groups_when_task_group_mismatch()
    {
        $task = $this->createTask();
        $incorrectGroup = $this->createGroup();

        $response = $this->get("/groups/{$incorrectGroup->id}/tasks/{$task->id}");
        $response->assertRedirectToRoute('group.show', [
            'group' => $incorrectGroup,
            'error' => 'Requested task does not belong to this group.',
        ]);
    }

    public function test_show_valid_task_data()
    {
        $task = $this->createTask();

        $response = $this->get("/groups/{$task->group_id}/tasks/{$task->id}");
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $task->group);
    }

    public function test_create_task_get_method_returns_view()
    {
        $group = $this->createGroup();

        $response = $this->get("/groups/{$group->id}/tasks/create");
        $response->assertOk();
        $response->assertViewIs('tasks.create');
    }

    public function test_create_task_does_not_work_with_invalid_data()
    {
        $group = $this->createGroup();

        $response = $this->post("/groups/{$group->id}/tasks/create");
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $taskPayload = [
            'title' => $this->faker->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->post("/groups/{$group->id}/tasks/create", $taskPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);

        $this->assertDatabaseMissing('tasks', $taskPayload);
    }

    public function test_create_task_works_with_valid_data()
    {
        $group = $this->createGroup();

        $taskPayload = [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];
        $response = $this->post("/groups/{$group->id}/tasks/create", $taskPayload);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'New task has been created.',
        ]);
        $this->assertDatabaseHas('tasks', $taskPayload);

        $this->assertEquals(TaskStatus::Pending, $group->tasks->last()->status);
        $this->assertNull($group->tasks->last()->completed_at);
    }

    public function test_delete_task_get_method_returns_view()
    {
        $task = $this->createTask();

        $response = $this->get("/groups/{$task->group_id}/tasks/{$task->id}/delete");
        $response->assertOk();
        $response->assertViewIs('tasks.delete');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $task->group);
    }

    public function test_delete_task_and_return_to_all_tasks()
    {
        $task = $this->createTask();

        $response = $this->delete("/groups/{$task->group_id}/tasks/{$task->id}/delete");
        $response->assertRedirectToRoute('group.show', [
            'group' => $task->group,
            'message' => 'Task is deleted.',
        ]);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function test_edit_task_get_method_returns_view()
    {
        $task = $this->createTask();

        $response = $this->get("/groups/{$task->group_id}/tasks/{$task->id}/edit");
        $response->assertOk();
        $response->assertViewIs('tasks.edit');
        $response->assertViewHas('task', $task);
    }

    public function test_edit_task_updates_the_task_and_return_to_show_task()
    {
        $task = $this->createTask();

        $validPayload = [
            'status' => $this->faker->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];

        $response = $this->put("/groups/{$task->group_id}/tasks/{$task->id}/edit", $validPayload);
        $response->assertRedirectToRoute('task.show', [
            'group' => $task->group,
            'task' => $task,
        ]);
    }

    public function test_edit_task_throw_errors_on_invalid_payload()
    {
        $task = $this->createTask();

        $response = $this->put("/groups/{$task->group_id}/tasks/{$task->id}/edit");
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->put("/groups/{$task->group_id}/tasks/{$task->id}/edit", [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $response = $this->put("/groups/{$task->group_id}/tasks/{$task->id}/edit", [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'completed',
        ]);
        $response = $this->put("/groups/{$task->group_id}/tasks/{$task->id}/edit", [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'Changing task status is not allowed once it is marked as completed.',
        ]);
    }
}
