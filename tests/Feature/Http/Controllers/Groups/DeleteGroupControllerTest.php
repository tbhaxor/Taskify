<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Enums\UserPermission;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $response = $this->get(route('group.delete', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_accessing_page_for_unauthorized_users()
    {
        // User is not associated with the group
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertForbidden();

        // User is associated with the group but doesn't have sufficient permission
        $role = Role::factory()
            ->withPermissions(UserPermission::VIEW_GROUPS)
            ->create(['user_id' => $user->id]);
        $user = User::factory()->withGroup($group, $role)->create();
        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.delete');
        $response->assertViewHas('group', $group);
    }

    public function test_should_delete_and_return_to_group_index_for_group_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('group.delete', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'message' => 'Group has been deleted.'
        ]);
    }

    public function test_should_return_view_on_get_method_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::DELETE_GROUPS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();

        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.delete');
        $response->assertViewHas('group', $group);
    }

    public function test_should_delete_and_return_to_group_index_for_authorized_users()
    {
        $group = Group::factory()->create();
        $role = Role::factory()
            ->withPermissions(UserPermission::DELETE_GROUPS)
            ->create(['user_id' => $group->user_id]);
        $user = User::factory()->withGroup($group, $role)->create();

        $response = $this->actingAs($user)->delete(route('group.delete', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'message' => 'Group has been deleted.'
        ]);
    }
}
