<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTaskControllerTest extends TestCase
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
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $group->owner->id]);

        $response = $this->get(route('task.show', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_viewing_tasks_of_groups_that_belongs_to_other_users()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id]);

        $response = $this->actingAs($user)->get(route('task.show', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_task_on_get_request(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->withHeader('Accept', 'application/json')->get(route('task.show', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $task);
        $response->assertViewHas('group', $task->group);
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

        $response = $this->actingAs($user)->get(route('task.show', [
            'group' => $group,
            'task' => 999999,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'error' => 'Requested task does not exist.'
        ]);
    }
}
