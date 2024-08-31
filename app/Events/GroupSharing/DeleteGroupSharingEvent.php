<?php

namespace App\Events\GroupSharing;

use App\Models\UserGroupRole;
use Illuminate\Foundation\Events\Dispatchable;

class DeleteGroupSharingEvent
{
    use Dispatchable;

    public function __construct(public readonly UserGroupRole $groupRole)
    {
        //
    }
}
