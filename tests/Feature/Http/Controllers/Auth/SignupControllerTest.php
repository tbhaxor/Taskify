<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;

class SignupControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        User::factory(1)->create();
    }

    public function test_should_return_view_on_get_method(): void
    {
        $response = $this->get(route('auth.signup'));
        $response->assertOk();
        $response->assertViewIs('auth.signup');
    }

    public function test_should_fail_on_invalid_payload(): void
    {
        $password = fake()->password();

        $response = $this->post(route('auth.signup'));
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
            'confirm_password' => 'The confirm password field is required.'
        ]);

        $response = $this->post(route('auth.signup'), [
            'name' => fake()->name(),
            'email' => User::all()->random(1)->first()->email,
            'password' => $password,
            'confirm_password' => $password,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.'
        ]);

        $response = $this->post(route('auth.signup'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $password,
            'confirm_password' => fake()->password(),
        ]);
        $response->assertSessionHasErrors([
            'confirm_password' => 'The confirm password field must match password.'
        ]);
    }

    public function test_should_redirect_to_login_page_on_success(): void
    {
        $password = fake()->password();

        $response = $this->post(route('auth.signup'), [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $password,
            'confirm_password' => $password,
        ]);
        $response->assertRedirectToRoute('auth.login');
    }
}
