<?php

namespace Tests\Feature\Http\Controllers\GroupSharing;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ListGroupSharingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('group-sharing.index', ['group' => $group]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_non_owner_to_view_group_sharing()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get(route('group-sharing.index', ['group' => $group]));
        $response->assertForbidden();
    }

    public function test_should_list_all_group_shares_for_group_owners()
    {
        /** @var Collection<int, User> $users */
        $users = User::factory()->count(10)->create();
        $owner = $users->first();

        $group = Group::factory()->create(['user_id' => $owner->id]);

        $users->slice(1)->each(function ($user) use ($group) {
            UserGroupRole::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'role_id' => 1
            ]);
        });

        User::factory()
            ->count(10)
            ->create()
            ->each(fn($user) => Group::factory()->create(['user_id' => $user->id]));

        $response = $this->actingAs($owner)->get(route('group-sharing.index', ['group' => $group]));
        $response->assertOk();
        $response->assertViewIs('group-sharing.index');
        $response->assertViewHasAll([
            'userGroupRoles' => UserGroupRole::whereGroupId($group->id)->get(),
            'group' => $group,
        ]);
        $targetUser = $users->slice(1)->random();

        $this->assertStringContainsString(
            '"' . route('group-sharing.edit', ['group' => $group, 'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($targetUser->id)->first()]) . '"',
            $response->content()
        );
        $this->assertStringContainsString(
            '"' . route('group-sharing.delete', ['group' => $group, 'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($targetUser->id)->first()]) . '"',
            $response->content()
        );
        $this->assertStringNotContainsString(
            '"' . route('group-sharing.edit', ['group' => $group, 'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($owner->id)->first()]) . '"',
            $response->content()
        );
        $this->assertStringNotContainsString(
            '"' . route('group-sharing.delete', ['group' => $group, 'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($owner->id)->first()]) . '"',
            $response->content()
        );
    }
}
