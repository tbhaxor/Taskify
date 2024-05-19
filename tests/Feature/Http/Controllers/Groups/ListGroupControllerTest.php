<?php

namespace Tests\Feature\Http\Controllers\Groups;

use App\Models\User;
use Tests\TestCase;

class ListGroupControllerTest extends TestCase
{
    public function test_should_redirect_to_login_page_when_unauthorized(): void
    {
        $response = $this->get(route('group.index'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_return_only_groups_associated_with_users(): void
    {
        $user = User::all()->random(1)->first();

        $response = $this->actingAs($user)->get(route('group.index'));
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', $user->groups->isEmpty() ? [] : $user->groups);
    }
}
