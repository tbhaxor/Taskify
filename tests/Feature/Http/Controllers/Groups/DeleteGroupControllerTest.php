<?php

namespace Tests\Feature\Http\Controllers\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class DeleteGroupControllerTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.delete', [
            'group' => 1
        ]));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_forbid_unauthorized_users(): void
    {
        $response = $this->actingAs($this->createUser())->get(route('group.delete', [
            'group' => $this->createGroup()
        ]));
        $response->assertForbidden();
    }

    public function test_should_return_view_on_get_method()
    {
        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->get(route('group.delete', [
            'group' => $group
        ]));
        $response->assertOk();
        $response->assertViewIs('groups.delete');
        $response->assertViewHas('group', $group);
    }

    public function test_should_delete_and_return_to_group_index()
    {

        $group = $this->createGroup();

        $response = $this->actingAs($group->user)->delete(route('group.delete', [
            'group' => $group
        ]));
        $response->assertRedirectToRoute('group.index', [
            'message' => 'Group has been deleted.'
        ]);
    }
}
