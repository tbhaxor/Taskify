<?php

namespace Tests\Feature\Http\Controllers\GroupSharing;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteGroupSharingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('group-sharing.delete', ['group' => $group, 'user_id' => $group->user_id]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_non_owner_to_delete_group_sharing()
    {
        $ownerUser = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $ownerUser->id]);
        $normalUser = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($normalUser)->get(route('group-sharing.delete', ['group' => $group, 'user_id' => $normalUser->id]));
        $response->assertForbidden();
    }

    public function test_should_forbid_owner_to_delete_group_sharing()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.delete', ['group' => $group, 'user_id' => $user->id]));
        $response->assertForbidden();
    }

    public function test_should_return_not_found_when_user_id_is_invalid()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.delete', ['group' => $group, 'user_id' => 999999]));
        $response->assertNotFound();
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->get(route('group-sharing.delete', ['group' => $group, 'user_id' => $user2->id]));
        $response->assertOk();
        $response->assertViewIs('group-sharing.delete');
        $response->assertViewHasAll([
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($user->id)->first(),
        ]);
    }

    public function test_should_delete_user_from_group_and_return_to_group_sharing_index()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->post(route('group-sharing.delete', ['group' => $group, 'user_id' => $user2->id]));
        $group->loadCount('users')->load('users');
        $this->assertDatabaseMissing('user_group_roles', [
            'user_id' => $user2->id,
            'group_id' => $group->id,
        ]);
        $this->assertTrue($group->users->first()->is($group->owner));
        $this->assertEquals(1, $group->users_count);
        $response->assertRedirectToRoute('group-sharing.index', [
            'group' => $group,
            'message' => 'Group sharing record has been deleted.'
        ]);

    }
}
