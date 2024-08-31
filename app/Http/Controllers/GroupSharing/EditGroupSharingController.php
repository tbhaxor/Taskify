<?php

namespace App\Http\Controllers\GroupSharing;

use App\Events\GroupSharing\EditGroupSharingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupSharing\EditGroupSharingRequest;
use App\Models\Group;
use App\Models\UserGroupRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EditGroupSharingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditGroupSharingRequest $request, Group $group, UserGroupRole $userGroupRole): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('group-sharing.edit', [
                'roles' => $request->user()->roles,
                'group' => $group,
                'userGroupRole' => $userGroupRole
            ]);
        }

        $userGroupRole->update($request->validated());

        EditGroupSharingEvent::dispatch($userGroupRole->fresh());

        return to_route('group-sharing.show', [
            'group' => $group,
            'userGroupRole' => $userGroupRole,
        ]);
    }
}
