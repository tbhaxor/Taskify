<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\Group;
use App\Models\User;
use Tests\TestCase;

class DeleteGroupControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory(10)->create();
        Group::factory(50)->create();
    }

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.delete', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_another_user_group(): void
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = Group::query()->where('user_id', '!=', $user->id)->get()->first();

        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();

        $response = $this->actingAs($user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.delete');
        $response->assertViewHas('group', $group);
    }

    public function test_should_delete_and_return_to_group_index()
    {
        $user = User::query()->whereHas('groups')->get()->first();
        $group = $user->groups->random(1)->first();

        $response = $this->actingAs($user)->delete(route('group.delete', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'message' => 'Group has been deleted.'
        ]);
    }
}
