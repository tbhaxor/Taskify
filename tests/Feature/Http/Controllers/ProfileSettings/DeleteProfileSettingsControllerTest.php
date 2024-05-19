<?php

namespace Tests\Feature\Http\Controllers\ProfileSettings;

use App\Models\User;
use Tests\TestCase;

class DeleteProfileSettingsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory(5)->create();
    }

    public function test_should_return_to_login_when_unauthenticated()
    {
        $response = $this->post(route('profile.delete'));
        $response->assertRedirectToRoute('auth.login');
    }

    public function test_should_delete_user_and_redirect_to_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.delete'));
        $response->assertRedirectToRoute('auth.logout');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
