<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $response = $this->get(route('group.index'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_only_groups_associated_with_users()
    {
        $user = User::factory()->create();
        $groups = Group::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', $groups);
    }

    public function test_should_also_return_shared_groups()
    {
        $user = User::factory()->create();
        $groups = Group::factory()->count(3)->create(['user_id' => $user->id]);

        $group = Group::factory()->create();
        UserGroupRole::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role_id' => 1
        ]);

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', $groups->push($group));
    }
}
