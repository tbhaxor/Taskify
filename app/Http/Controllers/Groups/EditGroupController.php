<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\EditGroupRequest;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EditGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EditGroupRequest $request, Group $group): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('groups.edit', [
                'group' => $group
            ]);
        }

        $group->update($request->validated());

        return to_route('group.show', [
            'group' => $group
        ]);
    }
}
