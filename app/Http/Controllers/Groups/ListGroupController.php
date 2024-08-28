<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\ListGroupRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ListGroupRequest $request): View|RedirectResponse
    {
        return view('groups.index', [
            'groups' => $request->user()->groups()->withCount('tasks')->get()
        ]);
    }
}
