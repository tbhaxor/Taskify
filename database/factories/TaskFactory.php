<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = Group::factory()->create();
        
        return [
            'title' => fake()->text(64),
            'description' => fake()->text(),
            'group_id' => $group->id,
            'user_id' => $group->user_id
        ];
    }
}
