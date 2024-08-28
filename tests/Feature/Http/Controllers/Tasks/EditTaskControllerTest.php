<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EditTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $task = Task::factory()->create();

        $response = $this->get(route('task.edit', [
            'group' => $task->group,
            'task' => $task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_editing_tasks_of_groups_that_belongs_to_other_users()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $group->user_id]);

        $response = $this->actingAs($user)->get(route('task.edit', [
            'group' => $group,
            'task' => $task
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

        $response = $this->actingAs($user)->get(route('task.edit', [
            'group' => $group,
            'task' => 999999,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'error' => 'Requested task does not exist.'
        ]);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);


        $response = $this->actingAs($user)->put(route('task.edit', [
            'group' => $group,
            'task' => $task,
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->actingAs($user)->put(route('task.edit', [
            'group' => $group,
            'task' => $task,
        ]), [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $this->actingAs($user)->put(route('task.edit', [
            'group' => $group,
            'task' => $task,
        ]), [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
            'status' => 'completed',
        ]);
        $response = $this->actingAs($user)->put(route('task.edit', [
            'group' => $group,
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

    public function test_should_accept_valid_payload_and_redirect_to_task_show()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['group_id' => $group->id, 'user_id' => $user->id]);

        $payload = [
            'status' => $this->faker->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];

        $response = $this->actingAs($user)->put(route('task.edit', [
            'group' => $group,
            'task' => $task,
        ]), $payload);

        $response->assertRedirectToRoute('task.show', [
            'group' => $group,
            'task' => $task,
        ]);
    }
}
