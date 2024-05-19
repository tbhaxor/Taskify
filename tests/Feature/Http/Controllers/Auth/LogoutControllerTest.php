<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    public function test_should_redirect_to_login_page_as_unauthenticated(): void
    {
        $response = $this->get(route('auth.logout'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_redirect_to_login_page_as_authenticated(): void
    {
        $response = $this->actingAs(User::all()->first())->get(route('auth.logout'));
        $response->assertRedirectToRoute('auth.login');
    }
}
