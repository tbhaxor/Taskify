<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserInvite>
 */
class UserInviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'group_id' => Group::factory()->create()->id,
            'role_id' => Role::query()->whereNull('user_id')->inRandomOrder()->first()->id
        ];
    }
}
