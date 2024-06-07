<?php

namespace Tests\Feature\Http\Controllers\ProfileSettings;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\TestHelper;

class UpdateProfileSettingsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, TestHelper;

    public function test_should_return_to_login_when_unauthenticated()
    {
        $response = $this->post(route('profile.edit'));
        $response->assertRedirectToRoute('auth.login');
    }

    /**
     * A basic feature test example.
     */
    public function test_should_return_view_on_get_method(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertViewIs('profile.edit');
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = $this->createUser();
        $anotherUser = $this->createUser();
        $password = $this->faker->password();
        $new_password = $this->faker->password();
        $user->update([
            'password' => Hash::make($password)
        ]);

        $executor = fn(array $data = []) => $this->actingAs($user)->post(route('profile.edit'), $data);

        $response = $executor();
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.'
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $this->faker->name(),
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.'
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $anotherUser->email,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.'
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'old_password' => $this->faker->password()
        ]);
        $response->assertSessionHasErrors([
            'old_password' => 'The password is incorrect.'
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'old_password' => $password,
        ]);
        $response->assertSessionHasErrors([
            'new_password' => 'The new password field is required.',
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'old_password' => $password,
            'new_password' => $new_password,
        ]);
        $response->assertSessionHasErrors([
            'confirm_new_password' => 'The confirm new password field is required.'
        ]);

        $response = $executor([
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'old_password' => $password,
            'new_password' => $new_password,
            'confirm_new_password' => $this->faker->password()
        ]);
        $response->assertSessionHasErrors([
            'confirm_new_password' => 'The confirm new password field must match new password.'
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $user = $this->createUser();
        $old_user = collect($user->toArray());
        $old_password = $this->faker->password();
        $new_password = $this->faker->password();
        $user->update([
            'password' => Hash::make($old_password)
        ]);
        $user = $user->fresh();
        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'old_password' => $old_password,
            'new_password' => $new_password,
            'confirm_new_password' => $new_password
        ];

        $response = $this->actingAs($user)->post(route('profile.edit'), $payload);
        $response->assertViewIs('profile.edit');
        $user = $user->refresh();
        $this->assertDatabaseMissing('users', [
            'email' => $old_user->get('email')
        ]);
        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);
        $this->assertNotEquals($user->email, $old_user->get('email'));
        $this->assertNotEquals($user->name, $old_user->get('name'));
    }
}
