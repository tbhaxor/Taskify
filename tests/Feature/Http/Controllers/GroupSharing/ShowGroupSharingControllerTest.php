<?php

namespace Tests\Feature\Http\Controllers\GroupSharing;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowGroupSharingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->first()
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_non_owner_to_show_group_sharing()
    {
        $user1 = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user1->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user2)->get(route('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($user2->id)->first()
        ]));
        $response->assertForbidden();
    }

    public function test_should_forbid_group_owners_to_show_group_sharing()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($user->id)->first()
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_view_and_data_on_get_method()
    {
        $user1 = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user1->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user1)->get(route('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => $userGroupRole = UserGroupRole::whereGroupId($group->id)->whereUserId($user2->id)->first()
        ]));
        $response->assertOk();
        $response->assertViewIs('group-sharing.show');
        $response->assertViewHasAll([
            'group' => $group,
            'userGroupRole' => $userGroupRole,
        ]);
    }
}
