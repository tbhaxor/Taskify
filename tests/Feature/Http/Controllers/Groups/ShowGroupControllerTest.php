<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Enums\UserPermission;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $response = $this->get(route('group.show', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_accessing_page_for_unauthorized_users()
    {
        // User is not associated with the group
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertForbidden();

        // User is associated with the group but doesn't have sufficient permission
        $role = Role::factory()
            ->withPermissions(UserPermission::DELETE_TASKS)
            ->create(['user_id' => $user->id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_group_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.show');
        $response->assertViewHas('group', $group);
    }

    public function test_should_return_valid_group_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::VIEW_GROUPS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.show');
        $response->assertViewHas('group', $group);
    }

    public function test_should_redirect_to_all_groups_on_invalid_group_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => 99999
        ]));
        $response->assertRedirectToRoute('group.index', [
            'error' => 'Requested resource does not exist.'
        ]);
    }
}
