<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class CreateTaskControllerTest extends TestCase
{
    protected Group $group;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->whereHas('groups')->get()->first();
        $this->group = $this->user->groups->random(1)->first();
    }

    public function test_should_redirect_to_login_page()
    {
        $response = $this->get(route('task.create', [
            'group' => $this->group
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method(): void
    {
        $response = $this->actingAs($this->user)->get(route('task.create', [
            'group' => $this->group
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.create');
    }

    public function test_should_reject_on_invalid_payload(): void
    {
        $response = $this->actingAs($this->user)->post(route('task.create', [
            'group' => $this->group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.'
        ]);

        $response = $this->actingAs($this->user)->post(route('task.create', [
            'group' => $this->group
        ]), [
            'title' => fake()->regexify('/[a-zA-Z0-9]{300}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.'
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $payload = [
            'title' => fake()->text(64),
            'description' => fake()->text(),
        ];

        $response = $this->actingAs($this->user)->post(route('task.create', [
            'group' => $this->group
        ]), $payload);
        $response->assertRedirectToRoute('group.show', [
            'group' => $this->group,
            'message' => 'New task has been created.'
        ]);
        $this->assertDatabaseHas('tasks', $payload);
    }
}
