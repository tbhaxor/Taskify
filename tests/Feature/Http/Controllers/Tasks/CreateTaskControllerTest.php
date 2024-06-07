<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class CreateTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_redirect_to_login_page()
    {
        $response = $this->get(route('task.create', [
            'group' => $this->createGroup()
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method(): void
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->get(route('task.create', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('tasks.create');
    }

    public function test_should_reject_on_invalid_payload(): void
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->post(route('task.create', [
            'group' => $group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.'
        ]);

        $response = $this->actingAs($group->user)->post(route('task.create', [
            'group' => $group
        ]), [
            'title' => $this->faker->regexify('/[a-zA-Z0-9]{300}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.'
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $group = $this->createGroup();
        $payload = [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];

        $response = $this->actingAs($group->user)->post(route('task.create', [
            'group' => $group
        ]), $payload);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'New task has been created.'
        ]);
        $this->assertDatabaseHas('tasks', $payload);
    }
}
