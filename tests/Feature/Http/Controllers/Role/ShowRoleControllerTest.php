<?php

namespace Tests\Feature\Http\Controllers\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowRoleControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthorized()
    {
        $response = $this->get(route('role.show', ['role' => 1]));
        $response->assertRedirectToRoute('auth.login');
    }

    /**
     * A basic feature test example.
     */
    public function test_should_return_role_in_view()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.show', ['role' => $user->roles()->first()]));

        $response->assertOk();
        $response->assertViewIs('roles.show');
        $response->assertViewHas('role');
        $this->assertTrue($response->viewData('role')->is($user->roles()->first()));
    }

    public function test_should_return_to_index_when_role_not_found()
    {
        $user = User::factory()->create();
        Role::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('role.show', ['role' => 99999]));

        $response->assertFound();
        $response->assertRedirectToRoute('role.index', [
            'error' => 'Requested resource does not exist.',
        ]);
    }

    public function test_should_forbid_users_to_view_other_users_roles()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user)->get(route('role.show', ['role' => $role]));

        $response->assertForbidden();
    }
}
