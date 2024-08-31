<?php

namespace Tests\Feature\Http\Controllers\GroupSharing;

use App\Events\GroupSharing\EditGroupSharingEvent;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EditGroupSharingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $group = Group::factory()->create();

        $response = $this->get(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->first()
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_non_owner_to_edit_group_sharing()
    {
        $ownerUser = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $ownerUser->id]);
        $normalUser = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($normalUser)->get(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($normalUser->id)->first()
        ]));
        $response->assertForbidden();
        Event::assertNotDispatched(EditGroupSharingEvent::class);
    }

    public function test_should_forbid_owner_to_edit_group_sharing()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => UserGroupRole::whereGroupId($group->id)->whereUserId($user->id)->first()
        ]));
        $response->assertForbidden();
        Event::assertNotDispatched(EditGroupSharingEvent::class);
    }

    public function test_should_redirect_to_group_sharing_index_when_id_is_invalid()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => 999999
        ]));
        $response->assertRedirectToRoute('group-sharing.index', [
            'group' => $group,
            'error' => 'Requested resource does not exist.'
        ]);
        Event::assertNotDispatched(EditGroupSharingEvent::class);
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->get(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => $userGroupRole = UserGroupRole::whereGroupId($group->id)->whereUserId($user2->id)->first()
        ]));
        $response->assertOk();
        $response->assertViewIs('group-sharing.edit');
        $response->assertViewHasAll([
            'group' => $group,
            'userGroupRole' => $userGroupRole,
        ]);
        Event::assertNotDispatched(EditGroupSharingEvent::class);
    }

    public function test_should_reject_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->post(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => $userGroupRole = UserGroupRole::whereGroupId($group->id)->whereUserId($user2->id)->first()
        ]));
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field is required.'
        ]);

        $response = $this->actingAs($user)->post(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => $userGroupRole
        ]), [
            'role_id' => $this->faker->text
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The role id field must be an integer.'
        ]);


        $response = $this->actingAs($user)->post(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => $userGroupRole
        ]), [
            'role_id' => 99999
        ]);
        $response->assertSessionHasErrors([
            'role_id' => 'The selected role id is invalid.'
        ]);
        Event::assertNotDispatched(EditGroupSharingEvent::class);
    }

    public function test_should_edit_user_from_group_and_return_to_group_sharing_show()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);
        /** @var User $user2 */
        $user2 = User::factory()->withGroup($group)->create();

        $response = $this->actingAs($user)->post(route('group-sharing.edit', [
            'group' => $group,
            'userGroupRole' => $userGroupRole = UserGroupRole::whereGroupId($group->id)->whereUserId($user2->id)->first()
        ]), [
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
        $response->assertRedirectToRoute('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => $userGroupRole,
        ]);
        Event::assertDispatched(EditGroupSharingEvent::class, fn($event) => $event->groupRole->is($userGroupRole));
    }
}
