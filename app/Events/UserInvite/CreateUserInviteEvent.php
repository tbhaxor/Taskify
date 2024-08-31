<?php

namespace App\Events\UserInvite;

use App\Models\UserInvite;
use Illuminate\Foundation\Events\Dispatchable;

class CreateUserInviteEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly UserInvite $invite)
    {
        //
    }
}
