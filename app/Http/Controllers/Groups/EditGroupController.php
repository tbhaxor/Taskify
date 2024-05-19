<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\EditGroupRequest;
use App\Models\Group;

class EditGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditGroupRequest $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('groups.edit', [
                'group' => $group
            ]);
        }

        $group->update($request->safe(['title', 'description']));

        return to_route('group.show', [
            'group' => $group
        ]);
    }
}
