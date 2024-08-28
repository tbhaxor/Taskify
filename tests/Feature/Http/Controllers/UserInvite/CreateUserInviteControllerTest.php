<?php

namespace Tests\Feature\Http\Controllers\UserInvite;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserInviteControllerTest extends TestCase
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

        $response = $this->actingAs($user)->get(route('user-invite.create', ['group' => $group]));
        $response->assertOk();
        $response->assertViewIs('user-invites.create');
        $response->assertViewHas('roles', $user->roles);
    }

    public function test_should_forbid_for_non_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('user-invite.create', ['group' => $group]));
        $response->assertForbidden();
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]));
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'role_id' => 'The role id field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]), [
            'email' => $this->faker->name(),
            'role_id' => $user->roles->first()->id
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.',
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]), [
            'email' => $this->faker->email(),
            'role_id' => $this->faker->text()
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field must be an integer.',
        ]);

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]), [
            'email' => $this->faker->email(),
            'role_id' => -1
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The selected role id is invalid.',
        ]);
    }

    public function test_should_not_create_user_invite_if_email_already_exists()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]), [
            'email' => $user2->email,
            'role_id' => $user->roles->first()->id
        ]);

        $this->assertDatabaseMissing('user_invites', [
            'email' => $user->email,
            'role_id' => $user->roles->first()->id,
            'group_id' => $group->id,
        ]);
        $response->assertRedirectToRoute('group.index', [
            'group' => $group,
            'message' => 'User has been invited to the group.'
        ]);
    }

    public function test_should_create_user_invite_if_email_does_not_exist()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $payload = [
            'email' => $this->faker->email(),
            'role_id' => $user->roles->first()->id,
            'group_id' => $group->id,
        ];

        $response = $this->actingAs($user)->post(route('user-invite.create', ['group' => $group]), $payload);
        $this->assertDatabaseHas('user_invites', $payload);
        $response->assertRedirectToRoute('user-invite.index', [
            'group' => $group,
            'message' => 'User has been invited to the group.'
        ]);
    }
}
