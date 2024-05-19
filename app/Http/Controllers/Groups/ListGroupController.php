<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\ListGroupRequest;

class ListGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ListGroupRequest $request)
    {
        if ($request->user()->groups->isEmpty()) {
            return view('groups.index', ['groups' => []]);
        }

        return view('groups.index', [
            'groups' => $request->user()->groups->toQuery()->withCount('tasks')->get(),
        ]);
    }
}
