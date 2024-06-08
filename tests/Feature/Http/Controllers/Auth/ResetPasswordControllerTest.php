<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_redirect_authenticated_users_to_groups(): void
    {

        $response = $this->actingAs($this->createUser())->get(route('auth.password.reset'));
        $response->assertRedirectToRoute('group.index');
    }

    public function test_should_return_view_on_get_method()
    {
        $user = $this->createUser();
        $token = Password::createToken($user);

        $response = $this->get(route('auth.password.reset', [
            'token' => $token,
            'email' => $user->email
        ]));
        $response->assertOk();
        $response->assertViewIs('auth.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $user->email);
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = $this->createUser();
        $token = Password::createToken($user);
        $password = $this->faker->password();

        $response = $this->post(route('auth.password.reset'));
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
            'token' => 'The token field is required.',
        ]);

        $response = $this->post(route('auth.password.reset'), [
            'email' => $this->faker->text(),
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.'
        ]);

        $response = $this->post(route('auth.password.reset'), [
            'email' => $this->faker->email(),
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The selected email is invalid.'
        ]);

        $response = $this->post(route('auth.password.reset'), [
            'email' => $user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $this->faker->password()
        ]);
        $response->assertSessionHasErrors([
            'password' => 'The password field confirmation does not match.'
        ]);

        $response = $this->post(route('auth.password.reset'), [
            'email' => $user->email,
            'token' => $token,
            'password' => '123',
            'password_confirmation' => '123'
        ]);
        $response->assertSessionHasErrors([
            'password' => 'The password field must be at least 6 characters.'
        ]);
    }

    public function test_should_fail_on_mismatch_token()
    {
        $user = $this->createUser();
        $token = Password::createToken($this->createUser());
        $password = $this->faker->password();

        $response = $this->post(route('auth.password.reset', [
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $user->email
        ]));

        $response->assertSessionHasErrors([
            'error' => 'This password reset token is invalid.'
        ]);
    }

    public function test_should_redirect_to_login_page_on_success()
    {
        Event::fake();

        $oldPassword = $this->faker->password();
        $user = $this->createUser([
            'password' => Hash::make($oldPassword)
        ]);
        $token = Password::createToken($user);
        $password = $this->faker->password();

        $response = $this->post(route('auth.password.reset'), [
            'email' => $user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password
        ]);
        $response->assertRedirectToRoute('auth.login', [
            'message' => __(Password::PASSWORD_RESET)
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $oldPassword
        ]);
        $response->assertSessionHasErrors([
            'credentials' => 'Invalid login credentials.'
        ]);

        $this->get(route('group.index'));

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password
        ]);
        $response->assertRedirectToRoute('group.index');

        Event::assertDispatched(PasswordReset::class);

    }

}
