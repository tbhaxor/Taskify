<?php

namespace App\Http\Controllers\UserInvite;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvite\EditUserInviteRequest;
use App\Models\Group;
use App\Models\UserInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EditUserInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditUserInviteRequest $request, Group $group, UserInvite $userInvite): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('user-invites.edit', [
                'userInvite' => $userInvite,
                'group' => $group,
                'roles' => $request->user()->roles
            ]);
        }

        $userInvite->update($request->validated());

        return to_route('user-invite.index', ['group' => $group, 'message' => 'User invite has been updated.']);
    }
}
