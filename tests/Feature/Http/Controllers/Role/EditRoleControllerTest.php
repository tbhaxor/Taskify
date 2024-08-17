<?php

namespace Tests\Feature\Http\Controllers\Role;

use App\Enums\UserPermission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class EditRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('role.edit', [
            'role' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_with_role_on_get_method(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('role.edit', ['role' => $role]));

        $response->assertOk();
        $response->assertViewIs('roles.edit');
        $response->assertViewHas('role', $role);
    }

    public function test_should_return_to_roles_index_when_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.edit', ['role' => 1000]));

        $response->assertRedirectToRoute('role.index', [
            'error' => 'Requested resource does not exist.',
        ]);
    }

    public function test_should_forbid_editing_other_users_roles(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user)->get(route('role.edit', ['role' => $role]));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $role]));
        $response->assertForbidden();
    }

    public function test_should_forbid_editing_default_roles(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('role.edit', ['role' => $user->roles()->first()]));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $user->roles()->first()]));
        $response->assertForbidden();
    }


    public function test_should_return_error_on_invalid_payload(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $role]));
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'permissions' => 'The permissions field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $role]), [
            'name' => '',
            'permissions' => ['create:tasks'],
        ]);
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $role]), [
            'name' => $this->faker->name(),
            'permissions' => ['hello_world'],
        ]);
        $response->assertSessionHasErrors([
            'permissions.0' => 'The selected permissions.0 is invalid.',
        ]);
    }

    public function test_should_edit_role_and_redirect_roles_index(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->withPermissions()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $role->name
        ]);

        $payload = [
            'name' => $this->faker->name(),
            'permissions' => $this->faker->randomElements(Arr::pluck(UserPermission::cases(), 'value'), 3)
        ];

        $response = $this->actingAs($user)->post(route('role.edit', ['role' => $role]), $payload);

        $response->assertRedirectToRoute('role.index');
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
            'name' => $role->name
        ]);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $payload['name']
        ]);
        $user->roles
            ->firstWhere('name', $payload['name'])
            ->permissions
            ->pluck('value.value')
            ->sort()
            ->each(fn($value,) => $this->assertTrue(in_array($value, $payload['permissions'])));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }
}
