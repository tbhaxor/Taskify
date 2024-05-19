<?php

namespace Tests\Feature\Http\Controllers\ProfileSettings;

use App\Models\User;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UpdateProfileSettingsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory(10)->create();
    }

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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertViewIs('profile.edit');
    }

    public function test_should_reject_on_invalid_payload()
    {
        $user = User::factory()->create();
        $password = fake()->password();
        $new_password = fake()->password();
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
            'name' => fake()->name(),
            'email' => fake()->name(),
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.'
        ]);

        $response = $executor([
            'name' => fake()->name(),
            'email' => User::where('email', '!=', $user->email)->get()->random(1)->first()->email,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.'
        ]);

        $response = $executor([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'old_password' => fake()->password()
        ]);
        $response->assertSessionHasErrors([
            'old_password' => 'The password is incorrect.'
        ]);

        $response = $executor([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'old_password' => $password,
        ]);
        $response->assertSessionHasErrors([
            'new_password' => 'The new password field is required.',
        ]);

        $response = $executor([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'old_password' => $password,
            'new_password' => $new_password,
        ]);
        $response->assertSessionHasErrors([
            'confirm_new_password' => 'The confirm new password field is required.'
        ]);

        $response = $executor([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'old_password' => $password,
            'new_password' => $new_password,
            'confirm_new_password' => fake()->password()
        ]);
        $response->assertSessionHasErrors([
            'confirm_new_password' => 'The confirm new password field must match new password.'
        ]);
    }

    public function test_should_accept_valid_payload()
    {
        $user = User::factory()->create();
        $old_user = collect($user->toArray());
        $old_password = fake()->password();
        $new_password = fake()->password();
        $user->update([
            'password' => Hash::make($old_password)
        ]);
        $user = $user->fresh();
        $payload = [
            'name' => fake()->name(),
            'email' => fake()->email(),
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
