<?php

namespace App\Http\Controllers\UserInvite;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvite\CreateUserInviteRequest;
use App\Models\Group;
use App\Models\User;
use App\Models\UserGroupRole;
use App\Models\UserInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CreateUserInviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateUserInviteRequest $request, Group $group): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('user-invites.create', [
                'roles' => $request->user()->roles
            ]);
        }

        if ($user = User::whereEmail($request->safe()->string('email'))->first()) {
            UserGroupRole::create([
                'user_id' => $user->id,
                'group_id' => $group->id,
                'role_id' => $request->safe()['role_id']
            ]);
            return to_route('group.index', ['group' => $group, 'message' => 'User has been invited to the group.']);
        } else {
            UserInvite::createOrFirst($request->safe()
                ->merge([
                    'group_id' => $group->id
                ])->toArray());
            return to_route('user-invite.index', ['group' => $group, 'message' => 'User has been invited to the group.']);
        }

    }
}
