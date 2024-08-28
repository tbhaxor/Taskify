<?php

namespace App\Http\Controllers\Groups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\CreateGroupRequest;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class CreateGroupController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws Throwable
     */
    public function __invoke(CreateGroupRequest $request): View|RedirectResponse
    {
        if ($request->isMethod('GET')) {
            return view('groups.create');
        }

        DB::transaction(function () use ($request) {
            $group = Group::create($request->safe()->merge(['user_id' => $request->user()->id])->toArray());
            $group->users()->attach($request->user()->id, [
                'role_id' => Role::admin()->id
            ]);
        });

        return to_route('group.index', [
            'message' => 'New group has been created.'
        ]);
    }
}
