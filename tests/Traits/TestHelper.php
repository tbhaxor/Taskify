<?php

namespace Tests\Traits;

use App\Models\Group;
use App\Models\Task;
use Illuminate\Support\Collection;

trait TestHelper
{
    private function createGroup(): Group
    {
        return Group::factory()->create();
    }

    /**
     * @param int $count
     * @return Collection<int, Group>
     */
    private function createGroups(int $count = 2): Collection
    {
        return Group::factory($count)->count($count)->create();
    }

    /**
     * @param bool $associateWithGroup
     * @return Task
     */
    private function createTask(bool $associateWithGroup = true): Task
    {
        if ($associateWithGroup) {
            return Task::factory()->create([
                'group_id' => $this->createGroup()->id
            ]);
        }

        return Task::factory()->create();
    }

}
