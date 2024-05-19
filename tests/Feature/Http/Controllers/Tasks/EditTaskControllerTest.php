<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class EditTaskControllerTest extends TestCase
{
    protected Group $group;
    protected Task $task;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory(2)->createMany();
        Group::factory(20)->createMany();
        Task::factory(50)->createMany();

        $this->user = User::query()->whereHas('groups', count: 2)->get()->first();
        $this->group = $this->user->groups->toQuery()->whereHas('tasks')->first();
        $this->task = $this->group->tasks->first();
    }


    public function test_should_redirect_to_login_page()
    {
        $response = $this->get(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_some_other_user()
    {
        $response = $this->actingAs(User::where('id', '!=', $this->group->user_id)->first())->get(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_as_missing_when_not_belongs_to_group()
    {
        /** @var Group $newGroup */
        $newGroup = $this->user->groups->toQuery()->where('id', '!=', $this->group->id)->get()->first();

        $response = $this->actingAs($this->user)->get(route('task.show', [
            'group' => $newGroup,
            'task' => $this->task
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $newGroup,
            'error' => 'Requested task does not exist.'
        ]);
    }

    public function test_should_return_as_missing_when_task_does_not_exist()
    {
        $this->task->delete();

        $response = $this->actingAs($this->user)->get(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $this->group,
            'error' => 'Requested task does not exist.'
        ]);

        $this->group = $this->user->groups->toQuery()->whereHas('tasks')->get()->first();
        $this->task = $this->group->tasks->first();
    }

    public function test_should_reject_on_invalid_payload()
    {
        $response = $this->actingAs($this->user)->put(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'status' => 'The status field is required.',
        ]);

        $response = $this->actingAs($this->user)->put(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]), [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'erroneous',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'The selected status is invalid.',
        ]);

        $this->actingAs($this->user)->put(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]), [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'completed',
        ]);
        $response = $this->actingAs($this->user)->put(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]), [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors([
            'status' => 'Changing task status is not allowed once it is marked as completed.',
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $payload = [
            'status' => fake()->randomElement([
                'pending',
                'in-progress',
                'completed',
            ]),
            'title' => fake()->text(64),
            'description' => fake()->text(),
        ];

        $response = $this->actingAs($this->user)->put(route('task.edit', [
            'group' => $this->group,
            'task' => $this->task,
        ]), $payload);
        $response->assertRedirectToRoute('task.show', [
            'group' => $this->group,
            'task' => $this->task,
        ]);
    }
}
