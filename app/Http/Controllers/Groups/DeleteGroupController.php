<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\DeleteGroupRequest;
use App\Models\Group;

class DeleteGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteGroupRequest $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('groups.delete', [
                'group' => $group
            ]);
        }

        $group->delete();

        return to_route('group.index', [
            'message' => 'Group has been deleted.'
        ]);
    }
}
