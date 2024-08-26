<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id]);

        $response = $this->get(route('task.delete', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_deleting_other_user_tasks()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->get(route('task.delete', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_as_missing_when_not_belongs_to_group()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $group2 = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group2->id]);


        $response = $this->actingAs($user)->get(route('task.show', [
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
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('task.delete', [
            'group' => $group,
            'task' => 9999,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'error' => 'Requested task does not exist.'
        ]);
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->get(route('task.delete', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.delete');
        $response->assertViewHas([
            'task' => $task,
            'group' => $group
        ]);
    }

    public function test_should_delete_task_and_return_to_group_show()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->delete(route('task.delete', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'Task is deleted.',
        ]);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }
}
