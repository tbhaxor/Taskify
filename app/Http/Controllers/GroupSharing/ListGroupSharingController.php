<?php

namespace App\Http\Controllers\GroupSharing;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListGroupSharingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group): View
    {
        return view('group-sharing.index', [
            'group' => $group,
            'userGroupRoles' => UserGroupRole::whereGroupId($group->id)->with(['user', 'group', 'role'])->get(),
        ]);
    }
}
