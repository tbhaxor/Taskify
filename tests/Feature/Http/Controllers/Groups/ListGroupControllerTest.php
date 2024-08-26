<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_should_redirect_to_login_page_when_unauthenticated(): void
    {
        $response = $this->get(route('group.index'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_only_groups_associated_with_users(): void
    {
        $user = User::factory()->create();
        $groups = Group::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', $groups);
    }
}
