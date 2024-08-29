<?php

namespace Tests\Feature\Http\Controllers\UserInvite;

use App\Models\Group;
use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserInviteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();
        $response = $this->get(route('user-invite.create', ['group' => $group]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->get(route('user-invite.delete', ['group' => $userInvite->group, 'userInvite' => $userInvite]));
        $response->assertOk();
        $response->assertViewIs('user-invites.delete');
        $response->assertViewHasAll([
            'userInvite' => $userInvite,
            'group' => $group,
        ]);
    }

    public function test_should_forbid_for_non_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.delete', ['group' => $group, 'userInvite' => $userInvite]));
        $response->assertForbidden();
    }

    public function test_should_redirect_to_user_invite_index_when_it_does_not_exist()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->post(route('user-invite.delete', ['group' => $group, 'userInvite' => 9999]));
        $response->assertRedirectToRoute('user-invite.index', [
            'group' => $group,
            'error' => 'Requested user invite does not exist.',
        ]);
    }

    public function test_should_delete_user_invite_and_return_to_user_invite_index()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.delete', ['group' => $userInvite->group, 'userInvite' => $userInvite]));
        $response->assertRedirectToRoute('user-invite.index', [
            'group' => $group,
            'message' => 'User invite has been deleted.',
        ]);
        $this->assertDatabaseMissing('user_invites', [
            'id' => $userInvite->id,
        ]);
    }
}
