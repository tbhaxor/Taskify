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

    /**
     * @param array<int, UserPermission> $permissions
     * @return $this
     */
    public function withPermissions(...$permissions): static
    {
        return $this->hasAttached(Permission::whereIn('value', $permissions)->get());
    }
}

