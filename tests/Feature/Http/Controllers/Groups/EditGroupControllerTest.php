<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class EditGroupControllerTest extends TestCase
{
    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.edit', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_another_user_group(): void
    {
        $user = User::all()->random(1)->first();
        $group = Group::query()->where('user_id', '!=', $user->id)->get()->first();

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

        $response = $this->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => fake()->regexify('/[A-Za-z0-9]{200}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();
        $title = fake()->text(64);

        $response = $this->actingAs($user)->put(route('group.edit', [
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
