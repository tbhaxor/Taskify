<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class ShowTaskControllerTest extends TestCase
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
        $response = $this->get(route('task.show', [
            'group' => $this->group,
            'task' => $this->task
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_some_other_user()
    {
        $response = $this->actingAs(User::where('id', '!=', $this->group->user_id)->first())->get(route('task.show', [
            'group' => $this->group,
            'task' => $this->task
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_task(): void
    {
        $response = $this->actingAs($this->user)->get(route('task.show', [
            'group' => $this->group,
            'task' => $this->task
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.show');
        $response->assertViewHas('task', $this->task);
        $response->assertViewHas('group', $this->group);
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

        $response = $this->actingAs($this->user)->get(route('task.show', [
            'group' => $this->group,
            'task' => $this->task,
        ]));
        $response->assertRedirectToRoute('group.show', [
            'group' => $this->group,
            'error' => 'Requested task does not exist.'
        ]);
    }
}
