<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ShowTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_redirect_to_login_page()
    {
        $task = $this->createTask();

        $response = $this->get(route('task.show', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_some_other_user()
    {
        $task = $this->createTask();

        $response = $this->actingAs($this->createUser())->get(route('task.show', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_task(): void
    {
        $task = $this->createTask();

        $response = $this->actingAs($task->user)->get(route('task.show', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $task->group);
    }

    public function test_should_return_as_missing_when_not_belongs_to_group()
    {
        $task = $this->createTask();
        $group = $this->createGroup();

        $response = $this->actingAs($task->user)->get(route('task.show', [
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

        $response = $this->actingAs($task->user)->get(route('task.show', [
            'group' => $task->group,
            'task' => $task,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $task->group,
            'error' => 'Requested task does not exist.'
        ]);
    }
}
