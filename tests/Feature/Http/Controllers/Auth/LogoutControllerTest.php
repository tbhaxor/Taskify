<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    public function test_should_redirect_to_login_page(): void
    {
        $response = $this->post(route('auth.logout'));
        $response->assertRedirectToRoute('auth.login');
    }
}
