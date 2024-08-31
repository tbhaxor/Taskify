<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function withGroup(Group $group, ?Role $role = null): Factory
    {
        /** @var Role $role */
        $role = $role ?? Role::admin();
        return $this->afterCreating(fn(User $user) => UserGroupRole::create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role_id' => $role->id
        ]));
    }
}
