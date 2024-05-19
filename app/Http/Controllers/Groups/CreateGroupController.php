<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\CreateGroupRequest;

class CreateGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateGroupRequest $request)
    {
        if ($request->isMethod('GET')) {
            return view('groups.create');
        }

        $request->user()->groups()->create($request->safe(['title', 'description']));

        return to_route('group.index', [
            'message' => 'New group has been created.'
        ]);
    }
}
