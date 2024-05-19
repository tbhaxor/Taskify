<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class CreateGroupControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory(10)->create();
        Group::factory(50)->create();
    }

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.create'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::all()->random(1)->first();

        $response = $this->actingAs($user)->get(route('group.create'));
        $response->assertOk();
        $response->assertViewIs('groups.create');
    }

    public function test_should_reject_invalid_values()
    {
        $user = User::all()->random(1)->first();

        $response = $this->actingAs($user)->post(route('group.create'), []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $groupPayload = [
            'title' => fake()->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->actingAs($user)->post(route('group.create'), $groupPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_accept_valid_values()
    {
        $user = User::all()->random(1)->first();
        $title = fake()->text(64);

        $response = $this->actingAs($user)->post(route('group.create'), [
            'title' => $title
        ]);
        $response->assertRedirectToRoute('group.index', [
            'message' => 'New group has been created.'
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title
        ]);
    }
}
