<?php

namespace App\Http\Controllers\GroupSharing;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupSharing\ListGroupSharingRequest;
use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\View\View;

class ListGroupSharingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ListGroupSharingRequest $request, Group $group): View
    {
        return view('group-sharing.index', [
            'group' => $group,
            'userGroupRoles' => UserGroupRole::whereGroupId($group->id)->with(['user', 'group', 'role'])->get(),
        ]);
    }
}
