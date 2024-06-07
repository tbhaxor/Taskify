<?php

namespace Tests\Feature\Http\Controllers\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class EditGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.edit', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_another_user_group(): void
    {
        $response = $this->actingAs($this->createUser())->get(route('group.edit', [
            'group' => $this->createGroup()
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->put(route('group.edit', [
            'group' => $group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

        $response = $this->actingAs($group->user)->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => $this->faker->regexify('/[A-Za-z0-9]{200}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $group = $this->createGroup();

        $title = $this->faker->text(64);

        $response = $this->actingAs($group->user)->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => $title
        ]);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group
        ]);
        $this->assertDatabaseMissing('groups', [
            'title' => $group->title
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title,
        ]);
    }
}
