<?php

namespace App\Http\Controllers\UserInvite;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvite\ListUserInviteRequest;
use App\Models\Group;
use App\Models\UserInvite;
use Illuminate\View\View;

class ListUserInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ListUserInviteRequest $request, Group $group): View
    {
        return view('user-invites.index', [
            'group' => $group,
            'userInvites' => UserInvite::query()
                ->where('group_id', $group->id)
                ->get()
        ]);
    }
}
