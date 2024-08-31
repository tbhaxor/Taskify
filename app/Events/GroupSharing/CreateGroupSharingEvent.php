<?php

namespace App\Events\GroupSharing;

use App\Models\UserGroupRole;
use Illuminate\Foundation\Events\Dispatchable;

class CreateGroupSharingEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly UserGroupRole $groupRole)
    {
        //
    }

}
