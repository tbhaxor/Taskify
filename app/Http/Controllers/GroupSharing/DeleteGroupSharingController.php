<?php

namespace App\Http\Controllers\GroupSharing;

use App\Events\GroupSharing\DeleteGroupSharingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupSharing\DeleteGroupSharingRequest;
use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeleteGroupSharingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteGroupSharingRequest $request, Group $group, UserGroupRole $userGroupRole): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('group-sharing.delete', [
                'group' => $group,
                'userGroupRole' => $userGroupRole
            ]);
        }

        $userGroupRole->delete();

        DeleteGroupSharingEvent::dispatch($userGroupRole);

        return to_route('group-sharing.index', [
            'group' => $group,
            'message' => 'Group sharing record has been deleted.'
        ]);
    }
}
