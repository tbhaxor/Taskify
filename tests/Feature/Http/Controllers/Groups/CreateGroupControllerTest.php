<?php

namespace Tests\Feature\Http\Controllers\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class CreateGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;


    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.create'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method()
    {
        $response = $this->actingAs($this->createUser())->get(route('group.create'));
        $response->assertOk();
        $response->assertViewIs('groups.create');
    }

    public function test_should_reject_invalid_values()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('group.create'), []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $groupPayload = [
            'title' => $this->faker->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->actingAs($user)->post(route('group.create'), $groupPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_accept_valid_values()
    {
        $title = $this->faker->text(64);

        $response = $this->actingAs($this->createUser())->post(route('group.create'), [
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
