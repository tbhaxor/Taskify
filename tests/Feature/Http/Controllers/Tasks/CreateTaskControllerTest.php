<?php

namespace Tests\Feature\Http\Controllers\Tasks;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('task.create', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('task.create', ['group' => $group]));
        $response->assertOk();
        $response->assertViewIs('tasks.create');
    }

    public function test_should_not_process_invalid_payload_and_return_validation_errors(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('task.create', ['group' => $group]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.'
        ]);

        $response = $this->actingAs($user)->post(route('task.create', ['group' => $group]), [
            'title' => $this->faker->regexify('/[a-zA-Z0-9]{300}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.'
        ]);
    }

    public function test_should_created_task_on_valid_payload_and_return_to_group_show()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $payload = [
            'title' => $this->faker->text(64),
            'description' => $this->faker->text(),
        ];

        $response = $this->actingAs($user)->post(route('task.create', ['group' => $group]), $payload);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group,
            'message' => 'New task has been created.'
        ]);
        $this->assertDatabaseHas('tasks', $payload);
    }
}
