<?php

namespace Tests\Feature\Http\Controllers\GroupSharing;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditGroupSharingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('group-sharing.edit', ['group' => $group, 'user_id' => $group->user_id]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_non_owner_to_edit_group_sharing()
    {
        $ownerUser = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $ownerUser->id]);
        $normalUser = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($normalUser)->get(route('group-sharing.edit', ['group' => $group, 'user_id' => $normalUser->id]));
        $response->assertForbidden();
    }

    public function test_should_forbid_owner_to_edit_group_sharing()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.edit', ['group' => $group, 'user_id' => $user->id]));
        $response->assertForbidden();
    }

    public function test_should_return_not_found_when_user_id_is_invalid()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.edit', ['group' => $group, 'user_id' => 999999]));
        $response->assertNotFound();
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->get(route('group-sharing.edit', ['group' => $group, 'user_id' => $user2->id]));
        $response->assertOk();
        $response->assertViewIs('group-sharing.edit');
        $response->assertViewHasAll([
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($user->id)->first(),
        ]);
    }

    public function test_should_reject_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->post(route('group-sharing.edit', ['group' => $group, 'user_id' => $user2->id]));
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field is required.'
        ]);

        $response = $this->actingAs($user)->post(route('group-sharing.edit', ['group' => $group, 'user_id' => $user2->id]), [
            'role_id' => $this->faker->text
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field must be an integer.'
        ]);


        $response = $this->actingAs($user)->post(route('group-sharing.edit', ['group' => $group, 'user_id' => $user2->id]), [
            'role_id' => 99999
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The selected role id is invalid.'
        ]);
    }

    public function test_should_edit_user_from_group_and_return_to_group_sharing_index()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        /** @var User $user2 */
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->post(route('group-sharing.edit', ['group' => $group, 'user_id' => $user2->id]), [
            'role_id' => 2
        ]);

        $this->assertDatabaseMissing('user_group_roles', [
            'user_id' => $user2->id,
            'group_id' => $group->id,
            'role_id' => 1
        ]);
        $this->assertDatabaseHas('user_group_roles', [
            'user_id' => $user2->id,
            'group_id' => $group->id,
            'role_id' => 2
        ]);
        $this->assertTrue($user2->fresh()->roleOnGroup($group)->first()->is(Role::find(2)));
        $response->assertRedirectToRoute('group-sharing.index', [
            'group' => $group,
            'message' => 'Group sharing has been updated.'
        ]);

    }
}
