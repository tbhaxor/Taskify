<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroupRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'user_id' => User::factory()->create()->id
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Group $group) {
            UserGroupRole::create([
                'group_id' => $group->id,
                'user_id' => $group->user_id,
                'role_id' => Role::admin()->id
            ]);
        });
    }
}
