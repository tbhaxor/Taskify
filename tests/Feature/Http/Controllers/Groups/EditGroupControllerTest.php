<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $response = $this->get(route('group.edit', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_editing_another_user_groups(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.edit', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]));
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => $this->faker->regexify('/[A-Za-z0-9]{200}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $title = $this->faker->text(64);

        $response = $this->actingAs($user)->put(route('group.edit', [
            'group' => $group
        ]), [
            'title' => $title
        ]);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group
        ]);
        $this->assertDatabaseMissing('groups', [
            'title' => $group->title
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title,
        ]);
    }
}
