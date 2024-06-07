<?php

namespace Tests\Feature\Http\Controllers\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ListGroupControllerTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.index'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_only_groups_associated_with_users(): void
    {
        $user = $this->createUser();
        $groups = $this->createGroups(attributes: ['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', $groups);
    }
}
