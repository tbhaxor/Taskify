<?php

namespace App\Http\Controllers\GroupSharing;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupSharing\ShowGroupSharingRequest;
use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\View\View;

class ShowGroupSharingController extends Controller
{
    public function __invoke(ShowGroupSharingRequest $request, Group $group, UserGroupRole $userGroupRole): View
    {
        return view('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => $userGroupRole,
        ]);
    }
}
