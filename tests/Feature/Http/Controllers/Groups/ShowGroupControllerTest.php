<?php

namespace Tests\Feature\Http\Controllers\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ShowGroupControllerTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.show', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_403_on_another_user_group(): void
    {
        $response = $this->actingAs($this->createUser())->get(route('group.show', [
            'group' => $this->createGroup()
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_valid_group(): void
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->get(route('group.show', [
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
        $group = $this->createGroup();
        $group->delete();

        $response = $this->actingAs($group->user)->get(route('group.show', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'error' => 'Requested resource does not exist.'
        ]);
    }
}
