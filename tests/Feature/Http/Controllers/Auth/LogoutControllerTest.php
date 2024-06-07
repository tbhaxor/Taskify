<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    public function test_should_redirect_to_login_page_as_unauthenticated(): void
    {
        $response = $this->get(route('auth.logout'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_redirect_to_login_page_as_authenticated(): void
    {
        $response = $this->actingAs($this->createUser())->get(route('auth.logout'));
        $response->assertRedirectToRoute('auth.login');
    }
}
