<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class ShowGroupControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory(10)->create();
        Group::factory(50)->create();
    }

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.show', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_another_user_group(): void
    {
        $user = User::all()->random(1)->first();
        $group = Group::query()->where('user_id', '!=', $user->id)->get()->first();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_group(): void
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.show');
        $response->assertViewHas([
            'group' => $group
        ]);
    }

    public function test_should_redirect_to_all_groups_on_invalid_group_id()
    {
        /** @var User $user */
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->first();
        $group->delete();

        $response = $this->actingAs($user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'error' => 'Requested resource does not exist.'
        ]);
    }
}
