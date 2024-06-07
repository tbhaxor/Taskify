<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class SignupControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_return_view_on_get_method(): void
    {
        $response = $this->get(route('auth.signup'));
        $response->assertOk();
        $response->assertViewIs('auth.signup');
    }

    public function test_should_fail_on_invalid_payload(): void
    {
        $password = $this->faker->password();

        $response = $this->post(route('auth.signup'));
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
            'confirm_password' => 'The confirm password field is required.'
        ]);

        $response = $this->post(route('auth.signup'), [
            'name' => $this->faker->name(),
            'email' => $this->createUser()->email,
            'password' => $password,
            'confirm_password' => $password,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.'
        ]);

        $response = $this->post(route('auth.signup'), [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $password,
            'confirm_password' => $this->faker->password(),
        ]);
        $response->assertSessionHasErrors([
            'confirm_password' => 'The confirm password field must match password.'
        ]);
    }

    public function test_should_redirect_to_login_page_on_success(): void
    {
        $password = $this->faker->password();
        $email = $this->faker->email();

        $response = $this->post(route('auth.signup'), [
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => $password,
            'confirm_password' => $password,
        ]);
        $response->assertRedirectToRoute('auth.login');
        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }
}
