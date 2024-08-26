<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $response = $this->get(route('group.delete', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_unauthorized_users(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->withHeader('Accept', 'application/json')->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
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

    public function test_should_delete_and_return_to_group_index()
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
}
