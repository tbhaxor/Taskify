<?php

namespace App\Http\Controllers\UserInvite;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvite\DeleteUserInviteRequest;
use App\Models\Group;
use App\Models\UserInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeleteUserInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteUserInviteRequest $request, Group $group, UserInvite $userInvite): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('user-invites.delete', [
                'group' => $group,
                'userInvite' => $userInvite
            ]);
        }

        $userInvite->delete();

        return to_route('user-invite.index', ['group' => $group, 'message' => 'User invite has been deleted.']);
    }
}
