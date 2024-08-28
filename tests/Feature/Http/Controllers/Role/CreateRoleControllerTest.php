<?php

namespace Tests\Feature\Http\Controllers\Role;

use App\Enums\UserPermission;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CreateRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('role.create'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_error_on_invalid_payload(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('role.create'));
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'permissions' => 'The permissions field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('role.create'), [
            'name' => '',
            'permissions' => ['create:tasks'],
        ]);
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);

        $response = $this->actingAs($user)->post(route('role.create'), [
            'name' => $this->faker->name(),
            'permissions' => ['hello_world'],
        ]);
        $response->assertSessionHasErrors([
            'permissions.0' => 'The selected permissions.0 is invalid.',
        ]);
    }

    public function test_should_create_role_and_attach_current_user_id(): void
    {
        $user = User::factory()->create();

        $payload = [
            "name" => $this->faker->name(),
            "permissions" => $this->faker->randomElements(Arr::pluck(UserPermission::cases(), 'value'), 3)
        ];

        $response = $this->actingAs($user)->post(route('role.create'), $payload);

        $response->assertFound();
        $response->assertRedirect(route('role.index'));

        $user->roles
            ->firstWhere('name', $payload['name'])
            ->permissions
            ->pluck('value.value')
            ->sort()
            ->each(fn($value,) => $this->assertTrue(in_array($value, $payload['permissions'])));
        $this->assertDatabaseHas("roles", [
            'name' => $payload['name'],
            'user_id' => $user->id,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }
}
