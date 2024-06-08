<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_redirect_authenticated_users_to_groups(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->createUser())->get(route('auth.password.forgot'));
        $response->assertRedirectToRoute('group.index');

        Notification::assertNothingSent();
    }

    public function test_should_return_view_on_get_method()
    {
        Notification::fake();

        $response = $this->get(route('auth.password.forgot'));
        $response->assertOk();
        $response->assertViewIs('auth.forgot-password');

        Notification::assertNothingSent();
    }

    public function test_should_throw_an_exception_when_invalid_email()
    {
        Notification::fake();

        $response = $this->post(route('auth.password.forgot'));
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.'
        ]);

        $response = $this->post(route('auth.password.forgot'), [
            'email' => $this->faker->text()
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.'
        ]);

        $response = $this->post(route('auth.password.forgot'), [
            'email' => $this->faker->email()
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The selected email is invalid.'
        ]);

        Notification::assertNothingSent();
    }

    public function test_should_return_view_on_success()
    {
        Notification::fake();

        $user = $this->createUser();

        $response = $this->post(route('auth.password.forgot'), [
            'email' => $user->email
        ]);
        $response->assertOk();
        $response->assertViewIs('auth.forgot-password');
        $response->assertViewHas('success', TRUE);
        $response->assertViewHas('message', __(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
