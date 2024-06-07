<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase, TestHelper, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_should_return_view_on_get_method(): void
    {
        $response = $this->get(route('auth.login'));
        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    public function test_should_redirect_authenticated_user_to_groups()
    {

        $response = $this->actingAs($this->createUser())->get(route('auth.login'));
        $response->assertRedirectToRoute('group.index');
    }

    public function test_should_fail_on_incorrect_credentials()
    {
        $response = $this->post(route('auth.login'), [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ]);
        $response->assertSessionHasErrors([
            'credentials' => 'Invalid login credentials.'
        ]);
    }

    public function test_should_fail_on_invalid_payload()
    {
        $response = $this->post(route('auth.login'));
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.'
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $this->faker->text(),
            'password' => $this->faker->password(),
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.',
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
            'remember' => $this->faker->text()
        ]);
        $response->assertSessionHasErrors([
            'remember' => 'The remember field must be true or false.'
        ]);
    }

    public function test_should_redirect_intended_on_successful_login()
    {

        $credentials = Collection::make($this->createUser()->only(['email', 'password']))
            ->merge([
                'remember' => $this->faker->randomElement([1, 0])
            ])
            ->toArray();
        $response = $this->post(route('auth.login'), $credentials);
        $response->assertRedirect('/');
    }

    public function test_should_redirect_to_groups_after_login()
    {
        $credentials = Collection::make($this->createUser()->only(['email', 'password']))
            ->merge([
                'remember' => $this->faker->randomElement([1, 0])
            ])
            ->toArray();

        $this->get('/groups');
        $response = $this->post(route('auth.login'), $credentials);
        $response->assertRedirectToRoute('group.index');
    }
}
