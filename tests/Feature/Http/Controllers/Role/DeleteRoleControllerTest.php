<?php

namespace Tests\Feature\Http\Controllers\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthorized()
    {
        $response = $this->get(route('role.delete', [
            'role' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_with_role_on_get_method()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('role.delete', ['role' => $role]));

        $response->assertOk();
        $response->assertViewIs('roles.delete');
        $response->assertViewHas('role', $role);
    }

    public function test_should_return_to_roles_index_when_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.delete', ['role' => 1000]));

        $response->assertRedirectToRoute('role.index', [
            'error' => 'Requested resource does not exist.',
        ]);
    }

    public function test_should_forbid_deleting_other_users_roles()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user)->get(route('role.delete', ['role' => $role]));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('role.delete', ['role' => $role]));
        $response->assertForbidden();
    }

    public function test_should_forbid_deleting_default_roles()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.delete', ['role' => $user->roles()->first()]));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('role.delete', ['role' => $user->roles()->first()]));
        $response->assertForbidden();
    }

    public function test_should_delete_role_and_redirect_roles_index()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id
        ]);

        $response = $this->actingAs($user)->post(route('role.delete', ['role' => $role]));

        $response->assertRedirectToRoute('role.index');
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }
}
