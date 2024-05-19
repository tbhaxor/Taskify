<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\ShowGroupRequest;
use App\Models\Group;

class ShowGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ShowGroupRequest $request, Group $group)
    {
        return view('groups.show', [
            'group' => $group
        ]);
    }
}
