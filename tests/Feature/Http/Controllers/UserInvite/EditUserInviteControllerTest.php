<?php

namespace Tests\Feature\Http\Controllers\UserInvite;

use App\Models\Group;
use App\Models\User;
use App\Models\UserInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditUserInviteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $userInvite = UserInvite::factory()->create();
        $response = $this->get(route('user-invite.edit', ['group' => $userInvite->group, 'userInvite' => $userInvite]));
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

        $response = $this->actingAs($user)->get(route('user-invite.edit', ['group' => $userInvite->group, 'userInvite' => $userInvite]));
        $response->assertOk();
        $response->assertViewIs('user-invites.edit');
        $response->assertViewHasAll([
            'userInvite' => $userInvite,
            'group' => $group,
            'roles' => $user->roles
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

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]));
        $response->assertForbidden();
    }

    public function test_should_redirect_to_user_invite_index_when_it_does_not_exist()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => 9999]));
        $response->assertRedirectToRoute('user-invite.index', [
            'group' => $group,
            'error' => 'Requested user invite does not exist.',
        ]);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]));
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]), [
            'email' => $this->faker->email(),
            'role_id' => $this->faker->text()
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field must be an integer.',
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]), [
            'email' => $this->faker->email(),
            'role_id' => -1
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The selected role id is invalid.',
        ]);
    }

    public function test_should_update_only_role_id_and_redirect_to_user_invite_index()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $userInvite = UserInvite::factory()->create([
            'group_id' => $group->id,
            'role_id' => $user->roles->first()->id,
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.edit', ['group' => $group, 'userInvite' => $userInvite]), [
            'email' => $this->faker->email(),
            'role_id' => $user->roles->last()->id
        ]);
        $this->assertDatabaseMissing('user_invites', [
            'role_id' => $user->roles->first()->id,
            'group_id' => $group->id,
        ]);
        $this->assertDatabaseHas('user_invites', [
            'role_id' => $user->roles->last()->id,
            'group_id' => $group->id,
        ]);
        $response->assertRedirectToRoute('user-invite.index', [
            'group' => $group,
            'message' => 'User invite has been updated.'
        ]);
    }
}
