<?php

namespace Database\Factories;

use App\Enums\UserPermission;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'user_id' => User::factory()->create()->id
        ];
    }

    public function withPermissions(int $count = 3): static
    {
        return $this->hasAttached(Permission::whereIn('value', UserPermission::all()->random($count))->get());
    }
}

