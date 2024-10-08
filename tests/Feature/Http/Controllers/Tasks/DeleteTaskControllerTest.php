<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Enums\UserPermission;
use App\Models\Group;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DeleteTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $group->user_id]);

        $response = $this->get(route('task.delete', [
            'group' => $group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_accessing_page_for_unauthorized_users()
    {
        // User is not associated with the group
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $group->user_id]);

        $response = $this->actingAs($user)->get(route('task.delete', [
            'group' => $group,
            'task' => $task,
        ]));
        $response->assertForbidden();

        // User is associated with the group but doesn't have sufficient permission
        $role = Role::factory()
            ->withPermissions(UserPermission::VIEW_GROUPS)
            ->create(['user_id' => $user->id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $response = $this->actingAs($user)->get(route('task.delete', [
            'group' => $group,
            'task' => $task,
        ]));
        $response->assertForbidden();
    }


    public function test_should_return_as_missing_when_not_belongs_to_group()
    {
        $user = User::factory()->create();
        /** @var Collection<int, Group> $groups */
        $groups = Group::factory()->count(2)->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $groups->last()->id, 'user_id' => $user->id]);


        $response = $this->actingAs($user)->get(route('task.show', [
            'group' => $groups->first(),
            'task' => $task
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $groups->first(),
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

    public function test_should_return_view_on_get_method_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

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

    public function test_should_delete_task_and_return_to_group_show_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

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

    public function test_should_return_view_on_get_method_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::DELETE_TASKS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

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

    public function test_should_delete_task_and_return_to_group_show_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::DELETE_TASKS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

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
