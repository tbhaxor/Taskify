<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }


    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $response = $this->get(route('group.create'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('group.create'));
        $response->assertOk();
        $response->assertViewIs('groups.create');
    }

    public function test_should_reject_invalid_values()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('group.create'), []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $groupPayload = [
            'title' => $this->faker->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->actingAs($user)->post(route('group.create'), $groupPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_create_group_on_valid_payload_and_redirect_to_all_groups()
    {
        $title = $this->faker->text(64);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('group.create'), [
            'title' => $title
        ]);

        $response->assertRedirectToRoute('group.index', [
            'message' => 'New group has been created.'
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title
        ]);
        $this->assertDatabaseHas('user_group_roles', [
            'user_id' => $user->id,
            'role_id' => Role::admin()->id,
            'group_id' => $user->groups->last()->id,
        ]);
    }
}
