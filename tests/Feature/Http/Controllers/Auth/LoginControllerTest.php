<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        User::factory(5)->create();
    }

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

        $response = $this->actingAs(User::all()->random(1)->first())->get(route('auth.login'));
        $response->assertRedirectToRoute('group.index');
    }

    public function test_should_fail_on_incorrect_credentials()
    {
        $response = $this->post(route('auth.login'), [
            'email' => fake()->email(),
            'password' => fake()->password(),
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
            'email' => fake()->text(),
            'password' => fake()->password(),
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.',
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => fake()->email(),
            'password' => fake()->password(),
            'remember' => fake()->text()
        ]);
        $response->assertSessionHasErrors([
            'remember' => 'The remember field must be true or false.'
        ]);
    }

    public function test_should_redirect_intended_on_successful_login()
    {
        $user = User::all()
            ->random(1)
            ->select(['email', 'password'])
            ->first();
        $credentials = Collection::make($user)
            ->merge([
                'remember' => fake()->randomElement([1, 0])
            ])
            ->toArray();
        $response = $this->post(route('auth.login'), $credentials);
        $response->assertRedirect('/');
    }

    public function test_should_redirect_to_groups_after_login()
    {
        $user = User::all()
            ->random(1)
            ->select(['email', 'password'])
            ->first();
        $credentials = Collection::make($user)
            ->merge([
                'remember' => fake()->randomElement([1, 0])
            ])
            ->toArray();

        $this->get('/groups');
        $response = $this->post(route('auth.login'), $credentials);
        $response->assertRedirectToRoute('group.index');
    }
}
