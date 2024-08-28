<?php

namespace App\Http\Controllers\GroupSharing;

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
    public function __invoke(EditGroupSharingRequest $request, Group $group): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('group-sharing.edit', [
                'roles' => $request->user()->roles,
                'group' => $group,
                'userGroupRole' => UserGroupRole::query()->where([
                    'group_id' => $group->id,
                    'user_id' => $request->query('user_id'),
                ])->firstOrFail()
            ]);
        }

        UserGroupRole::where([
            'group_id' => $group->id,
            'user_id' => $request->query('user_id'),
        ])->update($request->validated());

        return to_route('group-sharing.index', [
            'group' => $group,
            'message' => 'Group sharing has been updated.'
        ]);
    }
}
