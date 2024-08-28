<?php

namespace Tests\Feature\Http\Controllers\UserInvite;

use App\Models\Group;
use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListUserInviteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $userInvite = UserInvite::factory()->create();
        $response = $this->get(route('user-invite.index', ['group' => $userInvite->group]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_for_non_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->get(route('user-invite.index', ['group' => $group]));
        $response->assertForbidden();
    }

    public function test_should_list_all_the_invites_for_the_groups()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $userInvites = UserInvite::factory()->count(10)->create([
            'group_id' => $group->id,
        ]);

        UserInvite::factory()->count(10)->create();

        $response = $this->actingAs($user)->get(route('user-invite.index', ['group' => $group]));
        $response->assertOk();
        $response->assertViewIs('user-invites.index');
        $response->assertViewHasAll([
            'group' => $group,
            'userInvites' => $userInvites,
        ]);
    }
}
