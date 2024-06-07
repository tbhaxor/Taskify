<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class EditTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;


    public function test_should_redirect_to_login_page()
    {
        $task = $this->createTask();

        $response = $this->get(route('task.edit', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_some_other_user()
    {
        $task = $this->createTask();

        $response = $this->actingAs($this->createUser())->get(route('task.edit', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_as_missing_when_not_belongs_to_group()
    {
        $task = $this->createTask();
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->get(route('task.show', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'error' => 'Requested task does not exist.'
        ]);
    }

    public function test_should_return_as_missing_when_task_does_not_exist()
    {
        $task = $this->createTask();
        $task->delete();

        $response = $this->actingAs($task->user)->get(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $task->group,
            'error' => 'Requested task does not exist.'
        ]);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $task = $this->createTask();

        $response = $this->actingAs($task->user)->put(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->actingAs($task->user)->put(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]), [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $this->actingAs($task->user)->put(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]), [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'completed',
        ]);
        $response = $this->actingAs($task->user)->put(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]), [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'Changing task status is not allowed once it is marked as completed.',
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $task = $this->createTask();

        $payload = [
            'status' => $this->faker->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];

        $response = $this->actingAs($task->user)->put(route('task.edit', [
            'group' => $task->group,
            'task' => $task,
        ]), $payload);

        $response->assertRedirectToRoute('task.show', [
            'group' => $task->group,
            'task' => $task,
        ]);
    }
}
