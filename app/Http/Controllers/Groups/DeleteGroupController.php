<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\DeleteGroupRequest;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeleteGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DeleteGroupRequest $request, Group $group): View|RedirectResponse
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
