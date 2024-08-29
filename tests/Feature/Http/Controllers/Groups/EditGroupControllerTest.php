<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Enums\UserPermission;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $response = $this->get(route('group.edit', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_accessing_page_for_unauthorized_users()
    {
        // User is not associated with the group
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertForbidden();


        // User is associated with the group but doesn't have sufficient permission
        $role = Role::factory()
            ->withPermissions(UserPermission::VIEW_GROUPS)
            ->create(['user_id' => $user->id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => $this->faker->regexify('/[A-Za-z0-9]{200}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_edit_and_return_to_group_index_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $title = $this->faker->text(64);

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

    public function test_should_return_view_on_get_method_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::EDIT_GROUPS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_should_delete_and_return_to_group_index_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::EDIT_GROUPS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();

        $title = $this->faker->text(64);

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
