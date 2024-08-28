<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_redirect_to_login_page_when_unauthenticated()
    {
        $response = $this->get(route('group.show', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_fetching_details_of_other_user_groups()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_group_for_the_user_account()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.show');
        $response->assertViewHas('group', $group);
    }

    public function test_should_redirect_to_all_groups_on_invalid_group_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => 99999
        ]));
        $response->assertRedirectToRoute('group.index', [
            'error' => 'Requested resource does not exist.'
        ]);
    }
}
