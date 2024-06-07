<?php

namespace Tests\Traits;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

trait TestHelper
{
    /**
     * @param array $attributes Override the default factory definitions
     */
    private function createGroup(array $attributes = []): Group
    {
        $group = Group::factory()->create($attributes);

        // For some reason this is returned null but the user record exists
        // FIXME: This shouldn't be the case, investigation is required
        if (is_null($group->user)) {
            $group->user = User::findOrFail($group->user_id);
        }
        return $group;
    }

    /**
     * @param array $attributes Override the default factory definitions
     * @param int $count
     * @return Collection<int, Group>
     */
    private function createGroups(int $count = 2, array $attributes = []): Collection
    {
        return Group::factory($count)->count($count)->create($attributes);
    }

    private function createTask(): Task
    {
        return Task::factory()->create();
    }


    /**
     * @param array $attributes Override the default factory definitions
     */
    private function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

}
