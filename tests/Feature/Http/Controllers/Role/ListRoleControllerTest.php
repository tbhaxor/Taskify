<?php

namespace Tests\Feature\Http\Controllers\Role;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('role.index'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_default_roles_and_do_not_allow_edit_and_delete(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.index'));

        $response->assertOk();
        $response->assertViewIs('roles.index');
        $response->assertViewHas('roles');
        $response->assertDontSee(route('role.edit', $user->roles()->first()));
        $response->assertDontSee(route('role.delete', $user->roles()->first()));
        $this->assertEmpty(Role::withCount('permissions')->get()->diff($response->viewData('roles')));
        $this->assertNotNull($response->viewData('roles')->first()->permissions_count);
    }


    public function test_should_allow_edit_and_delete_for_user_roles(): void
    {
        $user = User::factory()->create()->loadCount('roles');
        $role = Role::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('role.index'));

        $response->assertOk();
        $response->assertViewIs('roles.index');
        $response->assertViewHas('roles');
        $this->assertCount($user->roles()->count(), $response->viewData('roles'));
        $response->assertDontSee(route('role.edit', $user->roles()->first()));
        $response->assertDontSee(route('role.delete', $user->roles()->first()));
        $response->assertSee(route('role.edit', $role));
        $response->assertSee(route('role.delete', $role));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

}
