<?php

namespace Tests\Feature\Http\Controllers\ProfileSettings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class DeleteProfileSettingsControllerTest extends TestCase
{

    use RefreshDatabase, TestHelper;

    public function test_should_return_to_login_when_unauthenticated()
    {
        $response = $this->post(route('profile.delete'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_delete_user_and_redirect_to_logout(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('profile.delete'));
        $response->assertRedirectToRoute('auth.logout');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
